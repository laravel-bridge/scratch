<?php

namespace LaravelBridge\Scratch\Concerns;

/**
 * @see \Illuminate\View\ViewServiceProvider
 */
trait SetupView
{
    /**
     * @param string|array $viewPath
     * @return static
     */
    public function appendViewPath($viewPath)
    {
        if (!is_array($viewPath)) {
            $viewPath = [$viewPath];
        }

        foreach ($viewPath as $path) {
            $this['config']->push('view.paths', $path);
        }

        $this['config']['view.paths'] = array_unique($this['config']['view.paths']);

        return $this;
    }

    /**
     * @param string|array $viewPath
     * @param string $compiledPath
     * @param bool $append View path will use append behavior when true
     * @return static
     */
    public function setupView($viewPath, string $compiledPath, $append = false)
    {
        $this->setupViewCompiledPath($compiledPath);

        if ($append) {
            return $this->appendViewPath($viewPath);
        }

        return $this->setupViewPath($viewPath);
    }

    /**
     * @param string $compiledPath
     * @return static
     */
    public function setupViewCompiledPath(string $compiledPath)
    {
        return $this->setupConfig('view.compiled', $compiledPath);
    }

    /**
     * @param string|array $viewPath
     * @return static
     */
    public function setupViewPath($viewPath)
    {
        return $this->setupConfig('view.paths', is_array($viewPath) ? $viewPath : [$viewPath]);
    }
}
