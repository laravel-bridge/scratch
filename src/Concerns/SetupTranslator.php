<?php

namespace LaravelBridge\Scratch\Concerns;

use Illuminate\Support\Facades;
use LaravelBridge\Scratch\Application;

trait SetupTranslator
{
    /**
     * @param string $locale
     * @return Application
     */
    public function setupLocale($locale): Application
    {
        $this['config']['app.locale'] = $locale;

        return $this;
    }

    /**
     * @param string $langPath
     * @return Application
     * @see TranslationServiceProvider
     */
    public function setupTranslator($langPath): Application
    {
        $this->instance('path.lang', $langPath);

        $this->alias('Lang', Facades\Lang::class);

        return $this;
    }
}
