<?php namespace Ionut\Crud\Providers;

use Doctrine\Common\Cache\PredisCache;
use Illuminate\Support\ServiceProvider;
use Predis\Client;

class CacheServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerRedis();

		$this->registerCache();
	}

	private function registerRedis()
	{
		$this->app->singleton('redis', function () {
			$manager = new Client();

			return $manager;
		});
	}

	private function registerCache()
	{
		$this->app->singleton('cache', function () {
			$manager = new PredisCache($this->app->redis);

			return $manager;
		});
	}
}