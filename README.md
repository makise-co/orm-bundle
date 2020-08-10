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
    'default'     => 'default',
    'databases'   => [
        'default' => [
            'connection' => 'pgsql'
        ],
    ],
    'connections' => [
        'pgsql' => [
            'driver'  => \MakiseCo\Database\Driver\MakisePostgres\MakisePostgresDriver::class,
            'connection' => 'host=host.docker.internal;port=5432;dbname=makise',
            'username' => 'makise',
            'password' => 'el-psy-congroo',
        ],
    ],

    'migrations' => [
        'table' => 'migrations',
        // migrations will be under App\\Migrations namespace
        'namespace' => 'App\\Migrations',
        // migrations will be placed in src/Migrations folder
        'directory' => dirname(__DIR__) . '/src/Migrations',
        'safe' => env('APP_ENV', 'production') !== 'production',
    ],

    'orm' => [
        'entityPath' => [
            dirname(__DIR__) . '/src/Entity',
        ],
    ],
];
```