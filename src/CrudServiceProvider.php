<?php namespace Ionut\Crud;

use Illuminate\Support\ServiceProvider;

class CrudServiceProvider extends ServiceProvider
{


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('crud', function () {
            return new Manager();
        });

        $this->app->singleton('crud.manager', function () {
            return $this->app->crud;
        });
    }
}