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

        $this->alias('View', Facades\View::class);

        return $this;
    }
}
