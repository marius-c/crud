<?php namespace Ionut\Crud\Laravel;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Ionut\Crud\GeneralException;

class Middleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if (app()->runningInConsole() || $response instanceof RedirectResponse) {
            return $response;
        }

        $content = $response instanceof Response ? $response->getContent() : $response->router->iframe();

        $this->checkDefinedConfig();

        return new Response(view(config('app.crud.layout'), [
            'title'   => config('app.crud.title'),
            'content' => $content
        ])->render());
    }

    public function checkDefinedConfig()
    {
        if (!config('app.crud.layout') || !config('app.crud.title')) {
            throw new GeneralException("You should define the 'app.crud.layout' and 'app.crud.title' config properties.");
        }
    }

}