<?php

use LaravelBridge\Scratch\Application;

require __DIR__ . '/vendor/autoload.php';

// Run `sqlite3 sqlite.db < db.sql` first
$connections = [
    'default' => [
        'driver' => 'sqlite',
        'database' => __DIR__ . '/sqlite.db',
    ],
];

Application::getInstance()
    ->setupDatabase($connections)
    ->bootstrap();
