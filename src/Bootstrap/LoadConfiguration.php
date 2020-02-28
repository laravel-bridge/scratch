<?php

namespace LaravelBridge\Scratch\Bootstrap;

use Illuminate\Config\Repository;
use LaravelBridge\Scratch\Application;
use LaravelBridge\Scratch\Contracts\Bootstrapper;
use Monolog\Handler\NullHandler;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @see https://github.com/laravel/framework/blob/v6.17.1/src/Illuminate/Foundation/Bootstrap/LoadConfiguration.php
 */
class LoadConfiguration implements Bootstrapper
{
    private const DEFAULT_CONFIG = [
        'logging' => [
            'channels' => [
                'null' => [
                    'driver' => 'monolog',
                    'handler' => NullHandler::class,
                ],
            ],
            'default' => 'null',
        ],
    ];

    public function bootstrap(Application $app): void
    {
        $app->instance('config', $config = new Repository(static::DEFAULT_CONFIG));

        $files = $this->getConfigurationFiles($app);

        foreach ($files as $key => $path) {
            $config->set($key, require $path);
        }

        date_default_timezone_set($config->get('app.timezone', 'UTC'));
    }

    protected function getConfigurationFiles(Application $app): array
    {
        $files = [];

        $configPath = $app['path.config'];

        if (!is_dir($configPath)) {
            return [];
        }

        /** @var SplFileInfo $file */
        foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file) {
            $directory = $this->getNestedDirectory($file, $configPath);

            $files[$directory . basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }

    /**
     * Get the configuration file nesting path.
     *
     * @param \SplFileInfo $file
     * @param string $configPath
     * @return string
     */
    protected function getNestedDirectory(SplFileInfo $file, $configPath): string
    {
        $directory = $file->getPath();

        if ($nested = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested) . '.';
        }

        return $nested;
    }
}
