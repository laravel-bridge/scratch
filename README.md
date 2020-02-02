# Composer Template

[![Build Status](https://travis-ci.com/laravel-bridge/scratch.svg?branch=master)](https://travis-ci.com/laravel-bridge/scratch)
[![codecov](https://codecov.io/gh/laravel-bridge/scratch/branch/master/graph/badge.svg)](https://codecov.io/gh/laravel-bridge/scratch)

For project from scratch.

## Installation

Run the following command to require package:

    composer require laravel-bridge/scratch

## Usage

Setup when you want to use the package

### Database

> Require `illuminate/database` and `illuminate/events`

[index.php](/examples/database/index.php) example for Database:

```php
use LaravelBridge\Scratch\Application;

$connections = [
    'default' => [
        'driver' => 'sqlite',
        'database' => __DIR__ . '/sqlite.db',
    ],
];

Application::getInstance()
    ->setupDatabase($connections)
    ->bootstrap();
```

### View

> Require `illuminate/view`

[index.php](/examples/view/index.php) example for View: 

```php
use LaravelBridge\Scratch\Application;

Application::getInstance()
    ->setupView(__DIR__, __DIR__ . '/compiled')
    ->bootstrap();

echo View::make('view', ['rows' => [1, 2, 3]]);
```

Template example [view.blade.php](/examples/view/view.blade.php):

```php
@foreach ($rows as $row)
    {{ $row }}
@endforeach
```
