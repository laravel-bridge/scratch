<?php

namespace LaravelBridge\Scratch\Concerns;

use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Pagination\PaginationServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\View\ViewServiceProvider;
use PDO;

trait SetupLaravel
{
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
            'Illuminate\Encryption\EncryptionServiceProvider',
            'Illuminate\Filesystem\FilesystemServiceProvider',
            'Illuminate\Foundation\Providers\FoundationServiceProvider',
            'Illuminate\Hashing\HashServiceProvider',
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
     * Setup user define provider.
     *
     * @param callable $callable The callable can return the instance of ServiceProvider
     * @return static
     */
    public function setupCallableProvider(callable $callable)
    {
        $serviceProvider = $callable($this, $this->config);
        $serviceProvider->register();

        if (method_exists($serviceProvider, 'boot') === true) {
            $this->call([$serviceProvider, 'boot']);
        }

        return $this;
    }

    /**
     * @param array $connections
     * @param string $default
     * @param int $fetch
     * @return static
     */
    public function setupDatabase(array $connections, $default = 'default', $fetch = PDO::FETCH_CLASS)
    {
        $this->config['database.connections'] = $connections;
        $this->config['database.default'] = $default;
        $this->config['database.fetch'] = $fetch;

        $this->register(DatabaseServiceProvider::class);

        return $this;
    }

    /**
     * @param string $locale
     *
     * @return static
     */
    public function setupLocale($locale)
    {
        $this->config['app.locale'] = $locale;

        return $this;
    }

    /**
     * @param bool $is
     *
     * @return static
     */
    public function setupRunningInConsole($is = true)
    {
        $this['runningInConsole'] = $is;

        return $this;
    }

    /**
     * @param string $langPath
     *
     * @return static
     */
    public function setupTranslator($langPath)
    {
        $this->instance('path.lang', $langPath);

        $this->register(TranslationServiceProvider::class);

        return $this;
    }

    /**
     * @param string|array $viewPath
     * @param string $compiledPath
     * @return static
     */
    public function setupView($viewPath, $compiledPath)
    {
        $this->config['view.paths'] = is_array($viewPath) ? $viewPath : [$viewPath];
        $this->config['view.compiled'] = $compiledPath;

        $this->register(ViewServiceProvider::class);

        return $this;
    }
}