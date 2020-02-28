<?php

namespace LaravelBridge\Scratch\Contracts;

use LaravelBridge\Scratch\Application;

interface Bootstrapper
{
    /**
     * @param Application $app
     */
    public function bootstrap(Application $app): void;
}
