<p align="center">
<img src="https://laravel-bridge.github.io/banner.svg" alt="banner">
</p>

<p align="center">
<a href="https://travis-ci.com/laravel-bridge/scratch"><img src="https://travis-ci.com/laravel-bridge/scratch.svg?branch=master" alt="Build Status"></a>
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

var_export(User::all()->toArray());
```

### View

> Require `illuminate/view`, require `illuminate/translation` when need translation.

[index.php](/examples/view/index.php) example for View: 

```php
use LaravelBridge\Scratch\Application;

Application::getInstance()
    ->setupTranslator(__DIR__ . '/lang')
    ->setupView(__DIR__, __DIR__ . '/compiled')
    ->bootstrap();

echo View::make('view', ['rows' => [1, 2, 3]]);
```

Template example [view.blade.php](/examples/view/view.blade.php):

```blade
@foreach ($rows as $row)
    {{ $row }}
@endforeach
```

## Example projects

* [Schemarkdown](https://github.com/MilesChou/schemarkdown)
* [Laravel Eloquent Generator](https://github.com/104corp/laravel-eloquent-generator)

## Thanks

* Idea by [@recca0120](https://github.com/recca0120/laravel-bridge)
* Logo by [@ycs77](https://github.com/ycs77)
