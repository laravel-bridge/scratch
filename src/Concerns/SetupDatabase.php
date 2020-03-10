<?php

namespace LaravelBridge\Scratch\Concerns;

trait SetupDatabase
{
    /**
     * @param string $name
     * @param array $connection
     * @param bool $default
     * @return static
     */
    public function setupDatabaseConfig(string $name, array $connection, bool $default = false)
    {
        $this->setupConfig("database.connections.{$name}", $connection);

        if ($default) {
            $this->setupConfig('database.default', $name);
        }

        return $this;
    }

    /**
     * @param array $connections
     * @param string $default
     * @return static
     */
    public function setupDatabaseConfigs(array $connections, string $default = 'default')
    {
        $this->setupConfig('database.connections', $connections);
        $this->setupConfig('database.default', $default);

        return $this;
    }
}
