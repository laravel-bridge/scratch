<?php

namespace LaravelBridge\Scratch\Concerns;

use Closure;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades;
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
    public function setupLogger(string $name, LoggerInterface $logger, bool $default = true)
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
    public function setupLoggerProvider(string $name, Closure $provider, bool $default = true)
    {
        $this->extend('log', function (LogManager $instance) use ($name, $provider) {
            return $instance->extend($name, $provider);
        });

        $this->setupLoggerConfig($name, $default);

        // Binding default concrete
        $this->bind(LogManager::class, 'log');

        // Set the default concrete for PSR-3 interface
        $this->bind(LoggerInterface::class, LogManager::class);

        if (!$this->isAlias(Facades\Log::class)) {
            $this->alias('Log', Facades\Log::class);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param bool $default
     * @return static
     */
    public function setupLoggerConfig(string $name, bool $default = true)
    {
        if ($default) {
            $this['config']['logging.default'] = $name;
        }

        $this['config']["logging.channels.{$name}"] = [
            'driver' => $name,
        ];

        return $this;
    }
}
