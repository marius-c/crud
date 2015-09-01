<?php namespace Ionut\Crud\Routing;

use ArrayAccess;
use Ionut\Crud\Controller;
use Ionut\Crud\Crud;
use Ionut\Crud\Modules\LaravelCompatibility\Request;
use Ionut\Crud\Utils\ArrayProxy;
use Symfony\Component\HttpFoundation\Response;

class Router extends ArrayProxy
{

    public $crud;

    public $controller;

    public $disableds = [];

    public $defaultCallback = null;

    protected $default = 'table';

    protected $collection;


    public function __construct(Crud $crud)
    {
        $this->crud = $crud;
        $this->controller = new Controller($this->crud);
        $this->collection = new RoutesCollection();
        $this->defaultCallback = [$this->controller, 'table'];

        foreach ($this->registerRoutes() as $name => $callback) {
            $this->addRoute($name, $callback);
        }
    }

    public function registerRoutes()
    {
        return [
            'processing' => [$this->controller, 'processing'],
            'validate'   => [$this->controller, 'validate'],
            'table'      => [$this->controller, 'table'],
            'action'     => [$this->controller, 'action'],
            'disabled'   => [$this->controller, 'disabled'],
            'default'    => $this->defaultAction()
        ];
    }

    public function defaultAction($change = null)
    {
        if (!$this->isAllowedDefault()) {
            return function () {
                return $this->crud->app['redirect']->back();
            };
        }

        if ($change) {
            $this->defaultCallback = $change;
        }

        return function () {
            return call_user_func($this->defaultCallback);
        };
    }

    /**
     * If another crud is called we dont allow other cruds to execute
     * the default action. That's the case when you have more
     * than one crud in the same page.
     */
    public function isAllowedDefault()
    {
        return !$this->crud->request['crud'];
    }

    public function addRoute($name, $callback)
    {
        $this->collection[$name] = new Route($this->crud, $name, $callback);
    }

    public function preload($url)
    {
        $backupRequest = $this->crud->app['request'];
        $this->crud->app['request'] = Request::create($url);
        $this->crud->app['request']->setSession($this->crud->app['session.store']);
        $response = $this->dispatch();
        $this->crud->app['request'] = $backupRequest;

        return $response;
    }

    public function dispatch()
    {
        $this->crud->boot();

        $route = $this->match();
        if (in_array($route->name, $this->disableds)) {
            $route = $this['disabled'];
        }

        $result = $this->crud->events->fire('before:route-dispatch');
        if ($result instanceof Response) {
            return $result;
        }

        $response = $route();

        $this->crud->events->fire('after:route-dispatch');

        return $response;
    }

    /**
     * @return $this
     */
    public function disableTableRoute()
    {
        $this->disableds[] = 'table';
    }

    /**
     * @return Route
     */
    public function match()
    {
        if ($this->crud->called()) {
            $action = $this->collection->get($this->crud->request->get('route'));
            if ($action) {
                return $action;
            }
        }

        return $this['default'];
    }

    /**
     * @return string
     */
    public function render()
    {
        $response = $this->dispatch();

        return $this->crud->kernel->dispatch($response);
    }

    public function iframe($url = null)
    {
        $this->defaultAction($this['iframe']);

        return $this->render();
    }

    /**
     * @throws \Exception
     * @return ArrayAccess
     */
    protected function getProxifiedArray()
    {
        return $this->collection;
    }
}