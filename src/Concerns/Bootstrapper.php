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
     * @return Application
     */
    public function setupBootstrapper(string $bootstrapper): Application
    {
        if (!in_array($bootstrapper, $this->bootstrappers, true)) {
            $this->bootstrappers[] = $bootstrapper;
        }

        return $this;
    }

    /**
     * @return Application
     */
    public function useConfigurationLoader(): Application
    {
        return $this->setupBootstrapper(ConfigurationLoader::class);
    }
}
