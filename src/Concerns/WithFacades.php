<?php

namespace LaravelBridge\Scratch\Concerns;

use Illuminate\Support\Facades\Facade;

trait WithFacades
{
    /**
     * Flag for aliases is registered
     *
     * @var bool
     */
    protected static $aliasesRegistered = false;

    /**
     * Register the facades for the application.
     *
     * @param array $customAliases
     * @return static
     * @see https://github.com/laravel/lumen-framework/blob/v6.3.4/src/Application.php#L705
     */
    public function withFacades(array $customAliases = [])
    {
        // Workaround for testing
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($this);

        $this->withAliases($customAliases);

        return $this;
    }

    /**
     * Register the aliases for the application.
     *
     * @param array $customAliases
     * @return static
     * @see https://github.com/laravel/lumen-framework/blob/v6.3.4/src/Application.php#L720
     */
    public function withAliases(array $customAliases = [])
    {
        if (static::$aliasesRegistered) {
            return $this;
        }

        static::$aliasesRegistered = true;

        $defaults = [
             'App' => 'Illuminate\Support\Facades\App',
             'Auth' => 'Illuminate\Support\Facades\Auth',
             'Arr' => 'Illuminate\Support\Arr',
             'Cache' => 'Illuminate\Support\Facades\Cache',
             'Config' => 'Illuminate\Support\Facades\Config',
             'DB' => 'Illuminate\Support\Facades\DB',
             'Eloquent' => 'Illuminate\Database\Eloquent\Model',
             'Event' => 'Illuminate\Support\Facades\Event',
             'File' => 'Illuminate\Support\Facades\File',
             'Gate' => 'Illuminate\Support\Facades\Gate',
             'Lang' => 'Illuminate\Support\Facades\Lang',
             'Log' => 'Illuminate\Support\Facades\Log',
             'Queue' => 'Illuminate\Support\Facades\Queue',
             'Schema' => 'Illuminate\Support\Facades\Schema',
             'Storage' => 'Illuminate\Support\Facades\Storage',
             'Str' => 'Illuminate\Support\Str',
             'URL' => 'Illuminate\Support\Facades\URL',
             'Validator' => 'Illuminate\Support\Facades\Validator',
             'View' => 'Illuminate\Support\Facades\View',
        ];

        foreach (array_merge($defaults, $customAliases) as $alias => $original) {
            if (class_exists($original)) {
                class_alias($original, $alias);
            }
        }

        return $this;
    }
}
