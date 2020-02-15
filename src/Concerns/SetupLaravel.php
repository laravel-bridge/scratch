<?php

namespace LaravelBridge\Scratch\Concerns;

use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\View\ViewServiceProvider;
use PDO;

trait SetupLaravel
{
    /**
     * Setup user define provider.
     *
     * @param callable $callable The callable can return the instance of ServiceProvider
     * @return static
     */
    public function setupCallableProvider(callable $callable)
    {
        return $this->setupServiceProvider($callable($this));
    }

    /**
     * Setup service provider.
     *
     * @param ServiceProvider|string $serviceProvider
     * @return static
     */
    public function setupServiceProvider($serviceProvider)
    {
        if (is_string($serviceProvider) && class_exists($serviceProvider)) {
            $serviceProvider = new $serviceProvider($this);
        }

        if (!$serviceProvider instanceof ServiceProvider) {
            throw new \RuntimeException('Argument $serviceProvider must extend ServiceProvider');
        }

        $serviceProvider->register();

        $this->serviceProviders[] = $serviceProvider;

        return $this;
    }

    /**
     * @param array $connections
     * @param string $default
     * @param int $fetch
     * @return static
     * @see DatabaseServiceProvider
     */
    public function setupDatabase(array $connections, $default = 'default', $fetch = PDO::FETCH_CLASS)
    {
        $this['config']['database.connections'] = $connections;
        $this['config']['database.default'] = $default;
        $this['config']['database.fetch'] = $fetch;

        $this->alias('DB', Facades\DB::class);
        $this->alias('Eloquent', EloquentModel::class);

        return $this;
    }

    /**
     * @param string $locale
     *
     * @return static
     */
    public function setupLocale($locale)
    {
        $this['config']['app.locale'] = $locale;

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
     * @return static
     * @see TranslationServiceProvider
     */
    public function setupTranslator($langPath)
    {
        $this->instance('path.lang', $langPath);

        $this->alias('Lang', Facades\Lang::class);

        return $this;
    }

    /**
     * @param string|array $viewPath
     * @param string $compiledPath
     * @return static
     * @see ViewServiceProvider
     */
    public function setupView($viewPath, $compiledPath)
    {
        $this['config']['view.paths'] = is_array($viewPath) ? $viewPath : [$viewPath];
        $this['config']['view.compiled'] = $compiledPath;

        $this->alias('View', Facades\View::class);

        return $this;
    }
}
