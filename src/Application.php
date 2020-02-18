<?php

namespace LaravelBridge\Scratch;

use Illuminate\Container\Container as LaravelContainer;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Fluent;
use Illuminate\Support\ServiceProvider;

class Application extends LaravelContainer
{
    use Concerns\SetupDatabase;
    use Concerns\SetupLog;
    use Concerns\SetupTranslator;
    use Concerns\SetupView;
    use Concerns\Workaround;

    /**
     * @var boolean
     */
    private $booted = false;

    /**
     * @var ServiceProvider[]
     */
    private $serviceProviders = [];

    public function __construct()
    {
        $this->instance(LaravelContainer::class, $this);
        $this->instance('config', new Fluent($this->defaultConfig()));

        if (class_exists(Request::class)) {
            $this->singleton('request', function () {
                return Request::capture();
            });
        }

        if (class_exists(Dispatcher::class)) {
            $this->singleton('events', Dispatcher::class);
        }

        $this->setupLaravelProviders();
    }

    /**
     * @return Application
     */
    public function bootstrap(): Application
    {
        if ($this->booted) {
            return $this;
        }

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
    protected function setupLaravelProviders(): void
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
}
