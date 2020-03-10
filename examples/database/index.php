<?php

use LaravelBridge\Scratch\Application;

require __DIR__ . '/vendor/autoload.php';

// Run `sqlite3 sqlite.db < db.sql` first
class User extends \Illuminate\Database\Eloquent\Model
{

}

$connections = [
    'driver' => 'sqlite',
    'database' => __DIR__ . '/sqlite.db',
];

$app = Application::getInstance()
    ->setupDatabaseConfig('default', $connections, true)
    ->bootstrap();

$user = new User();
$user->username = 'root';
$user->password = 'password';

$user->save();

var_export(User::all()->toArray());
