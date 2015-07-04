<?php namespace Ionut\Crud\Modules\Extractor;

use Illuminate\Support\ServiceProvider;

class ExtractorServiceProvider extends ServiceProvider {


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('extractor', function() {
			return new DbConfigExtractor($this->app);
		});
	}
}