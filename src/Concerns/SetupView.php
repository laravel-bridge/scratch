<?php

namespace LaravelBridge\Scratch\Concerns;

use LaravelBridge\Scratch\Application;

trait SetupView
{
    /**
     * @param string|array $viewPath
     * @param string $compiledPath
     * @return static
     * @see ViewServiceProvider
     */
    public function setupView($viewPath, $compiledPath)
    {
        $this->setupConfigs([
            'view.compiled' => $compiledPath,
            'view.paths' => is_array($viewPath) ? $viewPath : [$viewPath],
        ]);

        return $this;
    }
}
