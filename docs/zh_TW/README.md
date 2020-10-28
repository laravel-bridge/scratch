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

從無到有使用 Laravel 專案。

## 安裝方法

執行下面的指令來安裝套件：

    composer require laravel-bridge/scratch

## 使用方法

當需要套件的時候，再做設定即可。

套件間有可能會互相依賴，文件將會提醒需要安裝的對應套件

### Database

> 需要 `illuminate/database` 與 `illuminate/events` 套件

使用 `setupDatabaseConfig()` 可以設定一組資料庫連線。它有 3 個參數，下面是它的宣告方法：

```php
public function setupDatabaseConfig(string $name, array $connection, bool $default = false);
```

* `$name` 資料庫連線名稱
* `$connection` 資料庫連線設定
* `$default` 是否為預設的資料庫連線

`setupDatabaseConfigs()` 則是設定多組資料庫連線。它有 2 個參數，下面是它的宣告方法：

```php
public function setupDatabaseConfig(array $connections, string $default = 'default');
```

* `$connections` 是所有資料庫連線名稱對應資料庫連線設定
* `$default` 指定某個連線名稱為預設的資料庫連線

#### 範例

[/examples/database/index.php](/examples/database/index.php) 是一個 Database 的範例：

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

裡面也有 Eloquent Model 的範例：

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

> 需要 `illuminate/view` 套件，如果需要語系轉換的話，則需額外安裝 `illuminate/translation`。

[/examples/view/index.php](/examples/view/index.php) 是 View 的範例： 

```php
use LaravelBridge\Scratch\Application;

Application::getInstance()
    ->setupTranslator(__DIR__ . '/lang')
    ->setupView(__DIR__, __DIR__ . '/compiled')
    ->withFacades()
    ->bootstrap();

echo View::make('view', ['rows' => [1, 2, 3]]);
```

樣版範例則放在 [/examples/view/view.blade.php](/examples/view/view.blade.php):

```blade
@foreach ($rows as $row)
    {{ $row }}
@endforeach
```

### Logging

> 需要 `illuminate/log` 與 `illuminate/events` 套件

使用 `setupLogger()` 方法設定 Logger。它有 3 個參數，下面是它的宣告方法：

```php
public function setupLogger(string $name, LoggerInterface $logger, bool $default = false);
```

* `$name` Logger 的名稱。當使用 Facade 時，可以用這個名稱指定對應的 logger，如 `Log::driver('mylogger')`
* `$logger` [`Psr\Log\LoggerInterface`](https://www.php-fig.org/psr/psr-3/) 的實作
* `$default` 此 logger 是否為預設的 logger

下面是單元測試裡的範例：

```php
$spy = new TestHandler();

$logger = new Monolog\Logger('test');
$logger->pushHandler($spy);

$this->target->setupLogger('test', $logger, true)
    ->bootstrap();

Log::info('log_test');

$this->assertTrue($spy->hasInfoRecords());
```

### 自定義 Provider

除了使用 Laravel 官方的 provider 外，如果想使用自定義的 provider，可以使用 `setupProvider()` 方法，它的宣告方法如下：

```php
public function setupProvider($provider, bool $register = false, bool $boot = false): Application;
```

* `$provider` [ServiceProvider](https://github.com/laravel/framework/blob/8.x/src/Illuminate/Support/ServiceProvider.php) 的實例或 class 名稱
* `$register` 是否要馬上呼叫 `ServiceProvider::register()` 與相關的註冊方法
* `$boot` 是否要馬上呼叫 `ServiceProvider::boot()`

## 設定配置

設定會使用 `illuminate/config` 套件，下面是載入設定的優先順序：

1.  設定方法傳入的設定，優先權最高
2.  接著才是 bootstrap 階段載入的設定

## Facade

使用 `withFacades()` 方法，可以啟用 Facade 並註冊短名的 class：

```php
$app->withFacades();

View::make(); // It's works
```

## Bootstrap

Bootstrap 原本是 Laravel [Kernel](https://github.com/laravel/framework/blob/v7.1.0/src/Illuminate/Foundation/Http/Kernel.php#L37-L42) 的流程之一，下面是 Laravel 啟動器的執行順序。

```
\Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class
\Illuminate\Foundation\Bootstrap\LoadConfiguration::class
\Illuminate\Foundation\Bootstrap\HandleExceptions::class
\Illuminate\Foundation\Bootstrap\RegisterFacades::class
\Illuminate\Foundation\Bootstrap\RegisterProviders::class
\Illuminate\Foundation\Bootstrap\BootProviders::class
```

在 Scratch 的應用程式裡，我們 Bootstrap 先載入設定，並使用 `withFacades()` 來註冊 Facade，最後，透過呼叫 `bootstrap()` 來執行 `ServiceProvider::register()` 以及 `ServiceProvider::boot()`。這流程與上面 Laravel Kernel 的流程一模一樣。

`bootstrap()` 有一個參數為 `$withAllLaravelProviders`，預設為 `true`，它會注冊所有 Laravel 內建的 Service Provider。如果有哪個內建的 Service Provider 不想使用的話，那在呼叫 `bootstrap()` 前，先呼叫 `withoutLaravelProvider()` 方法把不想要的 Service Provider 排除。 

## 應用程式或套件的範例

應用程式：

* [Schemarkdown](https://github.com/MilesChou/schemarkdown)
* [Laravel Eloquent Generator](https://github.com/104corp/laravel-eloquent-generator)

套件:

* [Laravel Bridge - Slim](https://github.com/laravel-bridge/slim)

## Thanks

* Idea by [@recca0120](https://github.com/recca0120/laravel-bridge)
* Logo by [@ycs77](https://github.com/ycs77)
