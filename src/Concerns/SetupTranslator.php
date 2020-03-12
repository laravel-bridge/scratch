<?php

namespace LaravelBridge\Scratch\Concerns;

use Illuminate\Support\Facades;
use LaravelBridge\Scratch\Application;

trait SetupTranslator
{
    /**
     * @param string $locale
     * @return static
     */
    public function setupLocale($locale)
    {
        $this->setupConfig('app.locale', $locale);

        return $this;
    }

    /**
     * @param string $langPath
     * @return static
     * @see TranslationServiceProvider
     */
    public function setupTranslator($langPath)
    {
        $this->instance('path.lang', $langPath);

        $this->alias('Lang', Facades\Lang::class);

        return $this;
    }
}
