<?php

namespace LaravelBridge\Scratch;

use Illuminate\Config\Repository;
use Illuminate\Container\Container as LaravelContainer;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Log\LogManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Factory as ViewFactory;
use Monolog\Handler\NullHandler;
use Psr\Log\LoggerInterface;

class Application extends LaravelContainer
{
    use Concerns\Bootstrapper;
    use Concerns\SetupDatabase;
    use Concerns\SetupLog;
    use Concerns\SetupTranslator;
    use Concerns\SetupView;
    use Concerns\Workaround;

    private const DEFAULT_CONFIG = [
        'logging' => [
            'channels' => [
                'null' => [
                    'driver' => 'monolog',
                    'handler' => NullHandler::class,
                ],
            ],
            'default' => 'null',
        ],
    ];

    /**
     * @var string|null
     */
    private $basePath;

    /**
     * @var boolean
     */
    private $booted = false;

    /**
     * @var ServiceProvider[]
     */
    private $serviceProviders = [];

    public static function createFromBase(LaravelContainer $container): Application
    {
        if ($container instanceof self) {
            return $container;
        }

        $instance = new self();

        $instance->aliases = $container->aliases;
        $instance->resolved = $container->resolved;
        $instance->bindings = $container->bindings;
        $instance->instances = $container->instances;
        $instance->abstractAliases = $container->abstractAliases;

        return $instance;
    }

    /**
     * Application constructor.
     *
     * @param string|null $basePath
     */
    public function __construct($basePath = null)
    {
        if ($basePath) {
            $this->basePath = $basePath;

            $this->bindPathsInContainer();
        }

        // Binding self
        $this->instance(__CLASS__, $this);

        // Binding base container to self
        $this->instance(LaravelContainer::class, $this);

        // Initialize Config
        $this->instance('config', new Repository(static::DEFAULT_CONFIG));

        if (class_exists(Request::class)) {
            $this->singleton('request', function () {
                return Request::capture();
            });
        }

        if (class_exists(Dispatcher::class)) {
            $this->singleton('events', Dispatcher::class);
        }

        $this->setupLaravelProviders();
        $this->setupLaravelBinding();
    }

    /**
     * @return Application
     */
    public function bootstrap(): Application
    {
        if ($this->booted) {
            return $this;
        }

        // Run bootstrapper
        collect($this->bootstrappers)->each(function ($bootstrapper) {
            $this->make($bootstrapper)->bootstrap($this);
        });

        // Set the global instance
        LaravelContainer::setInstance($this);

        // Workaround for testing
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($this);

        foreach ($this->aliases as $class => $alias) {
            if (!class_exists($alias)) {
                class_alias($class, $alias);
            }
        }

        array_walk($this->serviceProviders, function ($provider) {
            if (method_exists($provider, 'boot')) {
                $this->call([$provider, 'boot']);
            }
        });

        $this->booted = true;

        return $this;
    }

    /**
     * @param ServiceProvider|string $provider
     * @return ServiceProvider|null
     * @see https://github.com/laravel/framework/blob/v6.13.1/src/Illuminate/Foundation/Application.php#L662
     */
    public function getProvider($provider): ?ServiceProvider
    {
        $name = is_string($provider) ? $provider : get_class($provider);

        $found = Arr::where($this->serviceProviders, function ($value) use ($name) {
            return $value instanceof $name;
        });

        return array_values($found)[0] ?? null;
    }

    /**
     * @param ServiceProvider|string $provider
     * @return ServiceProvider
     * @see https://github.com/laravel/framework/blob/v6.13.1/src/Illuminate/Foundation/Application.php#L603
     */
    public function register($provider): ServiceProvider
    {
        if ($registered = $this->getProvider($provider)) {
            return $registered;
        }

        if (is_string($provider)) {
            $provider = new $provider($this);
        }

        $provider->register();

        if (property_exists($provider, 'bindings')) {
            foreach ($provider->bindings as $key => $value) {
                $this->bind($key, $value);
            }
        }

        if (property_exists($provider, 'singletons')) {
            foreach ($provider->singletons as $key => $value) {
                $this->singleton($key, $value);
            }
        }

        $this->serviceProviders[] = $provider;

        return $provider;
    }

    /**
     * @inheritDoc
     */
    public function flush(): void
    {
        parent::flush();

        $this->bootstrappers = [];
        $this->serviceProviders = [];
    }

    /**
     * Setup user define provider.
     *
     * @param callable $callable The callable can return the instance of ServiceProvider
     * @return static
     */
    public function setupCallableProvider(callable $callable): Application
    {
        return $this->setupProvider($callable($this));
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return Application
     */
    public function setupConfig(string $key, $value): Application
    {
        $this['config'][$key] = $value;

        return $this;
    }

    /**
     * @param array $config
     * @return Application
     */
    public function setupConfigs(array $config): Application
    {
        foreach ($config as $key => $value) {
            $this->setupConfig($key, $value);
        }

        return $this;
    }

    /**
     * Setup user define provider.
     *
     * @param ServiceProvider|string $provider
     * @return static
     */
    public function setupProvider($provider): Application
    {
        $this->register($provider);

        return $this;
    }

    /**
     * Setup all LaravelProvider.
     */
    private function setupLaravelProviders(): void
    {
        collect([
            'Illuminate\Auth\AuthServiceProvider',
            'Illuminate\Broadcasting\BroadcastServiceProvider',
            'Illuminate\Bus\BusServiceProvider',
            'Illuminate\Cache\CacheServiceProvider',
            'Illuminate\Foundation\Providers\ConsoleSupportServiceProvider',
            'Illuminate\Cookie\CookieServiceProvider',
            'Illuminate\Database\DatabaseServiceProvider',
            'Illuminate\Events\EventServiceProvider',
            'Illuminate\Encryption\EncryptionServiceProvider',
            'Illuminate\Filesystem\FilesystemServiceProvider',
            'Illuminate\Foundation\Providers\FoundationServiceProvider',
            'Illuminate\Hashing\HashServiceProvider',
            'Illuminate\Log\LogServiceProvider',
            'Illuminate\Mail\MailServiceProvider',
            'Illuminate\Notifications\NotificationServiceProvider',
            'Illuminate\Pagination\PaginationServiceProvider',
            'Illuminate\Pipeline\PipelineServiceProvider',
            'Illuminate\Queue\QueueServiceProvider',
            'Illuminate\Redis\RedisServiceProvider',
            'Illuminate\Auth\Passwords\PasswordResetServiceProvider',
            'Illuminate\Session\SessionServiceProvider',
            'Illuminate\Translation\TranslationServiceProvider',
            'Illuminate\Validation\ValidationServiceProvider',
            'Illuminate\View\ViewServiceProvider',
        ])->filter(function ($provider) {
            return class_exists($provider);
        })->each(function ($provider) {
            $this->register($provider);
        });
    }

    /**
     * Setup all LaravelProvider.
     */
    private function setupLaravelBinding(): void
    {
        if ($this->has('events')) {
            $this->bind(Dispatcher::class, 'events');
            $this->bind(DispatcherContract::class, 'events');
        }

        if ($this->has('files')) {
            $this->bind(Filesystem::class, 'files');
        }

        if ($this->has('log')) {
            $this->bind(LogManager::class, 'log');
            $this->bind(LoggerInterface::class, LogManager::class);
        }

        if ($this->has('request')) {
            $this->bind(Request::class, 'request');
        }

        if ($this->has('view')) {
            $this->bind(ViewFactory::class, 'view');
            $this->bind(ViewFactoryContract::class, 'view');
        }
    }

    private function bindPathsInContainer(): void
    {
        $this->instance('path.base', $this->basePath);
        $this->instance('path.config', $this->basePath . DIRECTORY_SEPARATOR . 'config');
    }
}
