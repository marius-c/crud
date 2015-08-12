<?php namespace Ionut\Crud;

use Illuminate\Contracts\Routing\TerminableMiddleware;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Session\Middleware\StartSession;
use Ionut\Crud\Modules\LaravelCompatibility\Pipeline;

class HttpKernel
{

    protected $middleware = [
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        StartSession::class,
    ];

    /**
     * @var Crud
     */
    public $crud;

    /**
     * @param Crud $crud
     */
    public function __construct(Crud $crud)
    {
        $this->crud = $crud;
    }

    /**
     * Decorate the given response and throw it in the browser.
     *
     * @param  Response $response
     * @return Response
     */
    public function dispatch($response)
    {
        if ($this->fullDispatchAllowed()) {
            $response = $this->prepareResponse($response);
            $this->sendResponse($response);
        } else {
            if (method_exists($response, 'send')) {
                $this->sendResponse($response);
            }
        }

        return $response;
    }

    /**
     * @return mixed
     */
    public function fullDispatchAllowed()
    {
        return $this->crud->request['crud'] == $this->crud->id;
    }

    /**
     * @param $response
     * @return Response
     */
    private function prepareResponse($response)
    {
        if ( ! method_exists($response, 'send')) {
            if (is_array($response)) {
                $response = new JsonResponse($response);
            }
            else {
                $response = new Response($response);
            }

        }

        return $response;
    }

    /**
     * @param $response
     */
    private function sendResponse(Response $response)
    {
        $app = $this->crud->app->getBaseApp();
        $response = (new Pipeline($app))
            ->send($app['request'])
            ->through($this->middleware)
            ->then(function(Request $request) use($response) {
                return $response;
            });

        $this->crud->request->getSession()->save();

        $response->send();

        $this->terminate($app['request'], $response);
        exit;
    }

    public function terminate(Request $request, Response $response)
    {
        foreach ($this->middleware as $middleware)
        {
            $instance = $this->crud->app->make($middleware);

            if ($instance instanceof TerminableMiddleware)
            {
                $instance->terminate($request, $response);
            }
        }
    }


} 