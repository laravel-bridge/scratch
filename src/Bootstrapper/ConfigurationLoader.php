<?php

namespace LaravelBridge\Scratch\Bootstrapper;

use Illuminate\Config\Repository;
use LaravelBridge\Scratch\Application;
use LaravelBridge\Scratch\Contracts\Bootstrapper;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @see https://github.com/laravel/framework/blob/v6.17.1/src/Illuminate/Foundation/Bootstrap/LoadConfiguration.php
 */
class ConfigurationLoader implements Bootstrapper
{
    public function bootstrap(Application $app): void
    {
        /** @var Repository $config */
        $config = $app->make('config');

        $app->instance('config', new Repository());

        $files = $this->getConfigurationFiles($app);

        foreach ($files as $key => $path) {
            $app['config']->set($key, require $path);
        }

        foreach ($config->all() as $key => $item) {
            $app['config']->set($key, $item);
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
