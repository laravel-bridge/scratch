<?php

namespace LaravelBridge\Scratch\Concerns;

use LaravelBridge\Scratch\Application;
use LaravelBridge\Scratch\Bootstrapper\ConfigurationLoader;

trait Bootstrapper
{
    /**
     * @var array
     */
    protected $bootstrappers = [];

    /**
     * @param string $bootstrapper
     * @return static
     */
    public function setupBootstrapper(string $bootstrapper)
    {
        if (!in_array($bootstrapper, $this->bootstrappers, true)) {
            $this->bootstrappers[] = $bootstrapper;
        }

        return $this;
    }

    /**
     * @return static
     */
    public function useConfigurationLoader()
    {
        return $this->setupBootstrapper(ConfigurationLoader::class);
    }
}
