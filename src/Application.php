<?php namespace Ionut\Crud;

use Illuminate\Container\Container;
use Illuminate\Cookie\CookieServiceProvider;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Http\Exception\HttpResponseException;
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


/**
 * We are going to run our own laravel application. Isolation is one
 * the of main things that we're going to respect in this library.
 * We'll start first to isolate the laravel application.
 *
 * @property mixed request
 */
class Application extends Container
{
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
    public $baseProviders = [
        FilesystemServiceProvider::class,
        EventServiceProvider::class,
        ViewServiceProvider::class,
        RoutingServiceProvider::class,
        CrudServiceProvider::class,
        CookieServiceProvider::class,
        SessionServiceProvider::class,
        ExtractorServiceProvider::class,
        TranslationServiceProvider::class,
        CacheServiceProvider::class,
        LogServiceProvider::class,
        EncryptionServiceProvider::class,

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
        $this['config']['session'] = require __DIR__.'/../config/session.php';
    }

    public function removeProvider($provider)
    {
        $this->baseProviders = array_diff($this->baseProviders, [$provider]);
    }

    public function boot()
    {
        $this['app'] = $this;
        $this['env'] = 'production';
        $this->setPaths();

        $this->setCompatibility('5');

        if (file_exists($helpers = 'vendor/laravel/framework/src/Illuminate/Support/helpers.php')) {
            require $helpers;
        }

        $this->registerBaseProviders();

        $this->bootProviders();
        $this->bladeCompatibility();

        $this->bind('response', function () {
            return new Response();
        });

        $this['presenter'] = new Presenter;

        self::app('app', $this);

        $this->registerCoreContainerAliases();

        if ( ! $this->bound('request')) {
            $this->createIndependentRequest();
        }
    }

    public function setCompatibility($version)
    {
        $this['compatibility'] = new Compatibility($version);
    }

    public function setBaseApp(Container $app)
    {
        $this['base_app'] = $app;
        $this->useExistingRequest($app['request']);
    }

    public function getBaseApp()
    {
        return $this['base_app'] ?: $this;
    }

    public function createIndependentRequest()
    {
        $this['request'] = Request::capture();
    }

    public function useExistingRequest(\Illuminate\Http\Request $request)
    {
        if ($request->getSession()) {
            //
            //            $this['request'] = Request::capture();
            //            $this['request']->setSession($request->getSession());
            $this['request'] = clone $request;
            $this['session.store'] = $request->getSession();
        }
    }

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
            return $app[$k] = $v;
        }

        if ($k !== null) {
            return $app[$k];
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
        $default = include __DIR__.'/../config/default.php';
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
        $this['path.storage'] = $this['config']['path.storage'];
        $this['path.lang'] = __DIR__.'/../config/lang';
        $this['path'] = __DIR__.'/../config';
    }

    /**
     * Register the core class aliases in the container.
     *
     * @return void
     */
    public function registerCoreContainerAliases()
    {
        $aliases = array(
            'app'            => ['Illuminate\Foundation\Application', 'Illuminate\Contracts\Container\Container', 'Illuminate\Contracts\Foundation\Application'],
            'artisan'        => ['Illuminate\Console\Application', 'Illuminate\Contracts\Console\Application'],
            'auth'           => 'Illuminate\Auth\AuthManager',
            'auth.driver'    => ['Illuminate\Auth\Guard', 'Illuminate\Contracts\Auth\Guard'],
            'auth.password.tokens' => 'Illuminate\Auth\Passwords\TokenRepositoryInterface',
            'blade.compiler' => 'Illuminate\View\Compilers\BladeCompiler',
            'cache'          => ['Illuminate\Cache\CacheManager', 'Illuminate\Contracts\Cache\Factory'],
            'cache.store'    => ['Illuminate\Cache\Repository', 'Illuminate\Contracts\Cache\Repository'],
            'config'         => ['Illuminate\Config\Repository', 'Illuminate\Contracts\Config\Repository'],
            'cookie'         => ['Illuminate\Cookie\CookieJar', 'Illuminate\Contracts\Cookie\Factory', 'Illuminate\Contracts\Cookie\QueueingFactory'],
            'encrypter'      => ['Illuminate\Encryption\Encrypter', 'Illuminate\Contracts\Encryption\Encrypter'],
            'db'             => 'Illuminate\Database\DatabaseManager',
            'events'         => ['Illuminate\Events\Dispatcher', 'Illuminate\Contracts\Events\Dispatcher'],
            'files'          => 'Illuminate\Filesystem\Filesystem',
            'filesystem'     => 'Illuminate\Contracts\Filesystem\Factory',
            'filesystem.disk' => 'Illuminate\Contracts\Filesystem\Filesystem',
            'filesystem.cloud' => 'Illuminate\Contracts\Filesystem\Cloud',
            'hash'           => 'Illuminate\Contracts\Hashing\Hasher',
            'translator'     => ['Illuminate\Translation\Translator', 'Symfony\Component\Translation\TranslatorInterface'],
            'log'            => ['Illuminate\Log\Writer', 'Illuminate\Contracts\Logging\Log', 'Psr\Log\LoggerInterface'],
            'mailer'         => ['Illuminate\Mail\Mailer', 'Illuminate\Contracts\Mail\Mailer', 'Illuminate\Contracts\Mail\MailQueue'],
            'paginator'      => 'Illuminate\Pagination\Factory',
            'auth.password'  => ['Illuminate\Auth\Passwords\PasswordBroker', 'Illuminate\Contracts\Auth\PasswordBroker'],
            'queue'          => ['Illuminate\Queue\QueueManager', 'Illuminate\Contracts\Queue\Factory', 'Illuminate\Contracts\Queue\Monitor'],
            'queue.connection' => 'Illuminate\Contracts\Queue\Queue',
            'redirect'       => 'Illuminate\Routing\Redirector',
            'redis'          => ['Illuminate\Redis\Database', 'Illuminate\Contracts\Redis\Database'],
            'request'        => 'Illuminate\Http\Request',
            'router'         => ['Illuminate\Routing\Router', 'Illuminate\Contracts\Routing\Registrar'],
            'session'        => 'Illuminate\Session\SessionManager',
            'session.store'  => ['Illuminate\Session\Store', 'Symfony\Component\HttpFoundation\Session\SessionInterface'],
            'url'            => ['Illuminate\Routing\UrlGenerator', 'Illuminate\Contracts\Routing\UrlGenerator'],
            'validator'      => ['Illuminate\Validation\Factory', 'Illuminate\Contracts\Validation\Factory'],
            'view'           => ['Illuminate\View\Factory', 'Illuminate\Contracts\View\Factory'],
        );

        foreach ($aliases as $key => $aliases)
        {
            foreach ((array) $aliases as $alias)
            {
                $this->alias($key, $alias);
            }
        }
    }
}