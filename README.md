<p align="center">
<img src="https://laravel-bridge.github.io/banner.svg" alt="banner">
</p>

<p align="center">
<img src="https://github.com/laravel-bridge/scratch/workflows/tests/badge.svg" alt="tests">
<a href="https://codecov.io/gh/laravel-bridge/scratch"><img src="https://codecov.io/gh/laravel-bridge/scratch/branch/master/graph/badge.svg" alt="codecov"></a>
<a href="https://www.codacy.com/gh/laravel-bridge/scratch"><img src="https://api.codacy.com/project/badge/Grade/f0b586d036aa4924a343051339b9b433" alt="Codacy Badge"></a>
<a href="https://packagist.org/packages/laravel-bridge/scratch"><img src="https://poser.pugx.org/laravel-bridge/scratch/v/stable" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel-bridge/scratch"><img src="https://poser.pugx.org/laravel-bridge/scratch/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel-bridge/scratch"><img src="https://poser.pugx.org/laravel-bridge/scratch/license" alt="License"></a>
</p>

Start Laravel project from scratch.

## Installation

Run the following command to require package:

    composer require laravel-bridge/scratch

## Usage

Setup when you want to use the package

### Database

> Require `illuminate/database` and `illuminate/events`

Method `setupDatabaseConfig()` has 3 arguments, the following is signature:

```php
public function setupDatabaseConfig(string $name, array $connection, bool $default = false);
```

* `$name` is the database name.
* `$connection` is the database config only.
* `$default` will set the default database if true.

Method `setupDatabaseConfigs()` has 2 arguments, the following is signature:

```php
public function setupDatabaseConfig(array $connections, string $default = 'default');
```

* `$connections` is the all connections config.
* `$default` specify the connection is default.

#### Examples

[index.php](/examples/database/index.php) example for Database:

```php
use LaravelBridge\Scratch\Application;

$connections = [
    'driver' => 'sqlite',
    'database' => __DIR__ . '/sqlite.db',
];

$app = Application::getInstance()
    ->setupDatabaseConfig('default', $connections, true)
    ->bootstrap();
```

Eloquent is easy, too.

```php
use Illuminate\Database\Eloquent\Model;

class User extends Model
{

}

// ---

$user = new User();
$user->username = 'root';
$user->password = 'password';

$user->save();

User::all()->toArray();
```

### View

> Require `illuminate/view`, require `illuminate/translation` when need translation.

[index.php](/examples/view/index.php) example for View: 

```php
use LaravelBridge\Scratch\Application;

Application::getInstance()
    ->setupTranslator(__DIR__ . '/lang')
    ->setupView(__DIR__, __DIR__ . '/compiled')
    ->withFacades()
    ->bootstrap();

echo View::make('view', ['rows' => [1, 2, 3]]);
```

Template example [view.blade.php](/examples/view/view.blade.php):

```blade
@foreach ($rows as $row)
    {{ $row }}
@endforeach
```

### Logging

> Require `illuminate/log` and `illuminate/events`

Method `setupLogger()` has 3 arguments, the following is signature:

```php
public function setupLogger(string $name, LoggerInterface $logger, bool $default = false);
```

* `$name` is the Log name, and use Facade `Log::driver($name)` to specify.
* `$logger` is the instance implemented [`Psr\Log\LoggerInterface`](https://www.php-fig.org/psr/psr-3/).
* `$default` will set the default log driver if true.

Here is a testing example:

```php
$spy = new TestHandler();

$logger = new Monolog\Logger('test');
$logger->pushHandler($spy);

$this->target->setupLogger('test', $logger, true)
    ->bootstrap();

Log::info('log_test');

$this->assertTrue($spy->hasInfoRecords());
```

## Configuration

The configuration will use `illuminate/config` package. Following is the priority.

1. Setup method config or setup step
2. Configuration Loader or bootstrap step

## Facade

Use `withFacades()` to active Facade and register short class:

```php
$app->withFacades();

View::make(); // It's works
```

## Bootstrap

Bootstrap is a lifecycle in Laravel [Kernel](https://github.com/laravel/framework/blob/v7.1.0/src/Illuminate/Foundation/Http/Kernel.php#L37-L42). The following is bootstrapper order.

```
\Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class
\Illuminate\Foundation\Bootstrap\LoadConfiguration::class
\Illuminate\Foundation\Bootstrap\HandleExceptions::class
\Illuminate\Foundation\Bootstrap\RegisterFacades::class
\Illuminate\Foundation\Bootstrap\RegisterProviders::class
\Illuminate\Foundation\Bootstrap\BootProviders::class
```

In Scratch application, we can load config functionally. and use `withFacades()` to register Facade first. finally, call `ServiceProvider::register()` on every provider when call `bootstrap()`. Next, call `ServiceProvider::boot()` on every provider, just like Laravel Kernel.

`bootstrap()` has an argument `$withAllLaravelProviders`, register all laravel provider when true. Also, It's default true. However, use `withoutLaravelProvider()` if you don't want use some Laravel providers. 

## Example Projects or Libraries

Projects:

* [Schemarkdown](https://github.com/MilesChou/schemarkdown)
* [Laravel Eloquent Generator](https://github.com/104corp/laravel-eloquent-generator)

Libraries:

* [Laravel Bridge - Slim](https://github.com/laravel-bridge/slim)

## Thanks

* Idea by [@recca0120](https://github.com/recca0120/laravel-bridge)
* Logo by [@ycs77](https://github.com/ycs77)
