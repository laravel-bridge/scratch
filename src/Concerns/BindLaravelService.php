<?php

namespace LaravelBridge\Scratch\Concerns;

trait BindLaravelService
{
    /**
     * Setup all LaravelProvider.
     */
    private function bindLaravelService(): void
    {
        if ($this->has('db')) {
            $this->alias('db', \Illuminate\Database\DatabaseManager::class);
            $this->alias('db', \Illuminate\Database\ConnectionResolverInterface::class);
        }

        if ($this->has('events')) {
            $this->alias('events', \Illuminate\Events\Dispatcher::class);
            $this->alias('events', \Illuminate\Contracts\Events\Dispatcher::class);
        }

        if ($this->has('files')) {
            $this->alias('files', \Illuminate\Filesystem\Filesystem::class);
        }

        if ($this->has('log')) {
            $this->alias('log', \Illuminate\Log\LogManager::class);
            $this->alias('log', \Psr\Log\LoggerInterface::class);
        }

        if ($this->has('view')) {
            $this->alias('view', \Illuminate\View\Factory::class);
            $this->alias('view', \Illuminate\Contracts\View\Factory::class);
        }
    }
}
