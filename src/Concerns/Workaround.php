<?php

namespace LaravelBridge\Scratch\Concerns;

use Monolog\Handler\NullHandler;

trait Workaround
{
    /**
     * @var bool|null
     */
    private $isRunningInConsole;

    /**
     * @return bool
     * @see https://github.com/laravel/framework/blob/v6.15.1/src/Illuminate/Foundation/Application.php#L559
     */
    public function runningInConsole(): bool
    {
        if (null !== $this->isRunningInConsole) {
            return $this->isRunningInConsole;
        }

        if (isset($_ENV['APP_RUNNING_IN_CONSOLE'])) {
            return $this->isRunningInConsole = $_ENV['APP_RUNNING_IN_CONSOLE'] === 'true';
        }

        return $this->isRunningInConsole = (\PHP_SAPI === 'cli' || \PHP_SAPI === 'phpdbg');
    }

    /**
     * @param bool $is
     * @return static
     */
    public function setupRunningInConsole(bool $is = true)
    {
        $this->isRunningInConsole = $is;

        return $this;
    }
}
