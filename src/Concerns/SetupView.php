<?php

namespace LaravelBridge\Scratch\Concerns;

use Illuminate\Contracts\View\Factory as FactoryContract;
use Illuminate\Support\Facades;
use Illuminate\View\Factory;
use LaravelBridge\Scratch\Application;

trait SetupView
{
    /**
     * @param string|array $viewPath
     * @param string $compiledPath
     * @return Application
     * @see ViewServiceProvider
     */
    public function setupView($viewPath, $compiledPath): Application
    {
        $this['config']['view.paths'] = is_array($viewPath) ? $viewPath : [$viewPath];
        $this['config']['view.compiled'] = $compiledPath;

        // Binding default concrete
        $this->bind(Factory::class, 'view');

        // Set the default concrete for View Factory
        $this->bind(FactoryContract::class, Factory::class);

        $this->alias('View', Facades\View::class);

        return $this;
    }
}
