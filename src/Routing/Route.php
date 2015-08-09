<?php namespace Ionut\Crud\Routing;

use Ionut\Crud\Crud;

class Route
{

    public $name;

    /**
     * @var callable
     */
    public $callback;

    private $crud;

    public function __construct(Crud $crud, $name, $callback)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->crud = $crud;
    }

    public function url(array $appends = [])
    {
        return $this->crud->url([
                'route'  => $this->name,
                '_token' => csrf_token()
            ] + $appends);
    }

    /**
     * @param callable $callback
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    public function __invoke()
    {
        return call_user_func_array($this->callback, func_get_args());
    }
}