<?php namespace Ionut\Crud;

use Illuminate\Container\Container;
use Illuminate\Cookie\CookieServiceProvider;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Http\Response;
use Illuminate\Routing\RoutingServiceProvider;
use Illuminate\Session\SessionServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\View\ViewServiceProvider;
use Ionut\Crud\Modules\Extractor\ExtractorServiceProvider;
use Ionut\Crud\Modules\LaravelCompatibility\ApplicationCompatibility;
use Ionut\Crud\Modules\LaravelCompatibility\Request;
use Ionut\Crud\Providers\CacheServiceProvider;
use Ionut\Crud\Providers\LogServiceProvider;
use Ionut\Crud\View\Presenter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;



/**
 * We are going to run our own laravel application. Isolation is one
 * the of main things that we're going to respect in this library.
 * We'll start first to isolate the laravel application.
 *
 * @property mixed request
 */
class Application extends Container
{
	use ApplicationCompatibility;

	/**
	 * @var string
	 */
	public $name = 'The Crud Incubator';

	/**
	 * @var string
	 */
	public $version = 'v2.0.0';

	/**
	 * Our base providers container. We'll bootstrap them in the ->boot() method.
	 *
	 * @var array
	 */
	protected $baseProviders = [
		FilesystemServiceProvider::class,
		EventServiceProvider::class,
		ViewServiceProvider::class,
		DatabaseServiceProvider::class,
		RoutingServiceProvider::class,
		CrudServiceProvider::class,
		CookieServiceProvider::class,
		SessionServiceProvider::class,
		ExtractorServiceProvider::class,
		TranslationServiceProvider::class,
		CacheServiceProvider::class,
		LogServiceProvider::class,
	];

	/**
	 * @var array
	 */
	protected $providers;

	/**
	 * Register the config and the base providers.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerConfig();
		$this->registerBaseProviders();
	}

	/**
	 *
	 */
	public function boot()
	{
		$this['app'] = $this;
		$this['env'] = 'production';
		$this['request'] = $this->captureRequest();
		$this->setPaths();

		if (file_exists($helpers = 'vendor/laravel/framework/src/Illuminate/Support/helpers.php')) {
			require $helpers;
		}


		$this->registerBaseProviders();

		$this->bootProviders();
		$this->bladeCompatibility();

		$this['config']['session'] = require __DIR__ . '/../config/session.php';
		$this->bootRequest();
//		$this->bootSession();


		$this->bind('response', function() {
			return new Response();
		});

		$this['presenter'] = new Presenter;

		self::app('app', $this);
	}

	/**
	 *
	 */
	public function bladeCompatibility()
	{

		$compiler = $this['view']->getEngineResolver()->resolve('blade')->getCompiler();
		$compiler->setContentTags('{!!', '!!}');        // for variables and all things Blade
		$compiler->setEscapedContentTags('{{', '}}');   // for escaped data
	}

	/**
	 * @param null $k
	 * @param null $v
	 * @return null
	 */
	static public function app($k = null, $v = null)
	{
		static $app;

		if ($k == 'app') {
			$app = $v;
		}

		if ($v !== null) {
			return $app[ $k ] = $v;
		}

		if ($k !== null) {
			return $app[ $k ];
		}

		return $app;
	}

	/**
	 * @return bool
	 */
	public function runningInConsole()
	{
		return false;
	}

	/**
	 *
	 */
	public function registerConfig()
	{
		$default = include __DIR__ . '/../config/default.php';
		$this['config'] = new Config($default);
	}

	/**
	 *
	 */
	private function registerBaseProviders()
	{
		foreach ($this->baseProviders as $provider) {
			$this->attachProvider($provider);
		}
	}

	/**
	 * @param $class
	 */
	public function attachProvider($class)
	{
		$provider = new $class($this);
		$provider->register();
		$this->providers[] = $provider;
	}

	/**
	 *
	 */
	private function bootProviders()
	{
		array_walk($this->providers, function (ServiceProvider $provider) {
			$this->bootProvider($provider);
		});
	}

	/**
	 * Boot the given service provider.
	 *
	 * @param  \Illuminate\Support\ServiceProvider $provider
	 * @return void
	 */
	protected function bootProvider(ServiceProvider $provider)
	{
		if (method_exists($provider, 'boot')) {
			return $this->call([$provider, 'boot']);
		}
	}

	/**
	 * @return \Illuminate\Http\Request
	 */
	private function captureRequest()
	{
		Request::enableHttpMethodParameterOverride();

		return Request::createFromBase(\Symfony\Component\HttpFoundation\Request::createFromGlobals());
	}

	/**
	 *
	 */
	public function bootRequest()
	{
		$this['request']->setSession($this['session.store']);
	}

	/**
	 *
	 */
	public function bootSession()
	{
		$this['session.store']->setRequestOnHandler($this['request']);
		$this['session.store']->start();
	}

	/**
	 *
	 */
	private function setPaths()
	{
		$this['path.storage'] = __DIR__ . '/../storage';
		$this['path.lang'] = __DIR__ . '/../config/lang';
		$this['path'] = __DIR__ . '/../config';
	}



}