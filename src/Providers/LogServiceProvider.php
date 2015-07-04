<?php namespace Ionut\Crud\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LogServiceProvider extends ServiceProvider {


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('logger', function() {
			$logger = new Logger('Crud Logger');
			$logger->pushHandler(new StreamHandler($this->app['path.storage'].'/crud.log'));
			return $logger;
		});

		$this->app['events']->listen('illuminate.query', function($query, $bindings, $time) {
			$msg = json_encode(compact('query', 'bindings', 'time'), JSON_PRETTY_PRINT);
			$this->app['logger']->addDebug($msg);
		});
	}
}