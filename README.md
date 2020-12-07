# orm-bundle
[Cycle ORM](https://github.com/cycle/orm) integration bundle for MakiseCo Framework

## Installation
* Register [ORMProvider](src/ORMProvider.php)
* Register [commands](src/Console/Commands)

## Available commands
* `make:migration` - Create new migration
* `migrate` - Run database migrations
* `migrate:replay` - Replay (down, up) one or multiple migrations
* `migrate:rollback` - Rollback one (default) or multiple migrations
* `migrate:status` - Get list of all available migrations and their statuses

## Configuration

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
                'connect_timeout' => 0, // float, connection attempt timeout in seconds (0 means no timeout)

                // connection pool configuration
                'poolMinActive' => (int)env('DB_POOL_MIN_ACTIVE', 0), // The minimum number of established connections that should be kept in the pool at all times
                'poolMaxActive' => (int)env('DB_POOL_MAX_ACTIVE', 2), // The maximum number of active connections that can be allocated from this pool at the same time
                'poolMaxIdleTime' => (int)env('DB_POOL_MAX_IDLE_TIME', 30), // The minimum amount of time (seconds) a connection may sit idle in the pool before it is eligible for closing
                'poolMaxWaitTime' => 5.0, // The maximum number of seconds that can be awaited for a free connection from the pool
                'poolValidationInterval' => 15.0, // The number of seconds to sleep between runs of the idle connection validation/cleaner timer
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
