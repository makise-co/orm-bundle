# orm-bundle
[Cycle ORM](https://github.com/cycle/orm) integration bundle for MakiseCo Framework

## Usage
`ORMProvider` should be added to app provider.

Create new `database.php` config file in config folder:
```php
<?php

declare(strict_types=1);

use function MakiseCo\Env\env;

return [
    'default' => 'default',

    'databases' => [
        'default' => ['connection' => 'pgsql'],
    ],

    'connections' => [
        'pgsql' => [
            'driver' => \MakiseCo\Database\Driver\MakisePostgres\PooledMakisePostgresDriver::class,
            'options' => [
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', 5432),
                'username' => env('DB_USERNAME', 'makise'),
                'password' => env('DB_PASSWORD', 'el-psy-congroo'),
                'database' => env('DB_DATABASE', 'makise'),
                // or 'connection' => env('DB_URL', 'host=127.0.0.1;dbname=' . env('DB_NAME')),
                'schema' => ['public'],
                'timezone' => 'UTC',
                'charset' => 'utf8',
                'application_name' => 'MakiseCo Framework',

                // connector selects PostgreSQL Client implementation
                // Look for available implementations here https://github.com/makise-co/postgres
                'connector' => \MakiseCo\Postgres\Driver\Pq\PqConnector::class,
                // or use native PHP pgsql extension
                'connector' => \MakiseCo\Postgres\Driver\PgSql\PgSqlConnector::class,

                // connection pool configuration
                'poolMinActive' => (int)env('DB_POOL_MIN_ACTIVE', 0),
                'poolMaxActive' => (int)env('DB_POOL_MAX_ACTIVE', 2),
                'poolMaxIdleTime' => (int)env('DB_POOL_MAX_IDLE_TIME', 30),
                'poolValidationInterval' => 15.0,
            ],
        ],
    ],

    'migrations' => [
        'table' => 'migrations',
        'namespace' => 'App\\Migrations',
        'directory' => dirname(__DIR__) . '/src/Migrations',
        'safe' => env('APP_ENV', 'production') !== 'production',
    ],

    'orm' => [
        'entityPath' => [
            dirname(__DIR__) . '/src/Entity'
        ],
    ],
];
```
