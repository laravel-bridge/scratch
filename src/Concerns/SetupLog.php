<?php

namespace LaravelBridge\Scratch\Concerns;

use Closure;
use Illuminate\Log\LogManager;
use Psr\Log\LoggerInterface;

/**
 * @see https://github.com/laravel/framework/blob/v6.15.1/src/Illuminate/Log/LogManager.php
 */
trait SetupLog
{
    /**
     * @param string $name
     * @param LoggerInterface $logger
     * @param bool $default
     * @return static
     */
    public function setupLogger(string $name, LoggerInterface $logger, bool $default = false)
    {
        return $this->setupLoggerProvider($name, function () use ($logger) {
            return $logger;
        }, $default);
    }

    /**
     * @param string $name
     * @param Closure $provider
     * @param bool $default
     * @return static
     */
    public function setupLoggerProvider(string $name, Closure $provider, bool $default = false)
    {
        $this->extend('log', function (LogManager $instance) use ($name, $provider) {
            return $instance->extend($name, $provider);
        });

        $this->setupLoggerConfig($name, $default);

        return $this;
    }

    /**
     * @param string $name
     * @param bool $default
     * @return static
     */
    public function setupLoggerConfig(string $name, bool $default = false)
    {
        $this->setupConfig("logging.channels.{$name}", [
            'driver' => $name,
        ]);

        if ($default) {
            $this->setupConfig('logging.default', $name);
        }

        return $this;
    }
}
