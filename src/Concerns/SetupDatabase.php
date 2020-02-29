<?php

namespace LaravelBridge\Scratch\Concerns;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades;
use PDO;

trait SetupDatabase
{
    /**
     * @param array $connections
     * @param string $default
     * @param int $fetch
     * @return static
     * @see DatabaseServiceProvider
     */
    public function setupDatabase(array $connections, $default = 'default', $fetch = PDO::FETCH_CLASS)
    {
        $this->setupConfigs([
            'database.connections' => $connections,
            'database.default' => $default,
            'database.fetch' => $fetch,
        ]);

        $this->alias('DB', Facades\DB::class);
        $this->alias('Eloquent', EloquentModel::class);

        return $this;
    }
}
