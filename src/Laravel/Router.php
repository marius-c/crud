<?php namespace Ionut\Crud\Laravel;

use Illuminate\Http\Response;
use Ionut\Crud\Crud;

class Router extends \Illuminate\Routing\Router
{
    public function prepareResponse($request, $response)
    {
        if ($response instanceof Crud) {
            return $response->render();
        }

        return parent::prepareResponse($request, $response);
    }
}