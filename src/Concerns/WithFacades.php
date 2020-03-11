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
     * @return static
     * @see https://github.com/laravel/lumen-framework/blob/v6.3.4/src/Application.php#L705
     */
    public function withFacades($customAliases = [])
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
    public function withAliases($customAliases = [])
    {
        if (static::$aliasesRegistered) {
            return $this;
        }

        static::$aliasesRegistered = true;

        $defaults = [
            'Illuminate\Support\Facades\App' => 'App',
            'Illuminate\Support\Facades\Auth' => 'Auth',
            'Illuminate\Support\Arr' => 'Arr',
            'Illuminate\Support\Facades\Cache' => 'Cache',
            'Illuminate\Support\Facades\Config' => 'Config',
            'Illuminate\Support\Facades\DB' => 'DB',
            'Illuminate\Database\Eloquent\Model' => 'Eloquent',
            'Illuminate\Support\Facades\Event' => 'Event',
            'Illuminate\Support\Facades\File' => 'File',
            'Illuminate\Support\Facades\Gate' => 'Gate',
            'Illuminate\Support\Facades\Lang' => 'Lang',
            'Illuminate\Support\Facades\Log' => 'Log',
            'Illuminate\Support\Facades\Queue' => 'Queue',
            'Illuminate\Support\Facades\Schema' => 'Schema',
            'Illuminate\Support\Facades\Storage' => 'Storage',
            'Illuminate\Support\Str' => 'Str',
            'Illuminate\Support\Facades\URL' => 'URL',
            'Illuminate\Support\Facades\Validator' => 'Validator',
            'Illuminate\Support\Facades\View' => 'View',
        ];

        foreach (array_merge($defaults, $customAliases) as $original => $alias) {
            if (class_exists($original)) {
                class_alias($original, $alias);
            }
        }

        return $this;
    }
}
