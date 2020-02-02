<?php

use LaravelBridge\Scratch\Application;

require __DIR__ . '/vendor/autoload.php';

Application::getInstance()
    ->setupView(__DIR__, __DIR__ . '/compiled')
    ->bootstrap();

echo View::make('view', ['rows' => [1, 2, 3]]);
