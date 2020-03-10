<?php

namespace LaravelBridge\Scratch\Concerns;

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
        $this->setupConfigs([
            'view.compiled' => $compiledPath,
            'view.paths' => is_array($viewPath) ? $viewPath : [$viewPath],
        ]);

        return $this;
    }
}
