<?php
/*
 * This file is part of the Makise-Co Framework
 *
 * World line: 0.571024a
 * (c) Dmitry K. <coder1994@gmail.com>
 */

declare(strict_types=1);

return [
    'default' => 'default',

    'databases' => [
        'default' => ['connection' => 'pgsql'],
    ],

    'connections' => [
        'pgsql' => [
            'driver' => \MakiseCo\Database\Driver\MakisePostgres\PooledMakisePostgresDriver::class,
            'options' => [
                'host' => 'host.docker.internal',
                'port' => 5432,
                'username' => 'makise',
                'password' => 'el-psy-congroo',
                'database' => 'makise',
                // or 'connection' => env('DB_URL', 'host=127.0.0.1;dbname=' . env('DB_NAME')),
                'schema' => ['public'],
                'timezone' => 'UTC',
                'charset' => 'utf8',
                'application_name' => 'MakiseCo Framework',

                'connector' => \MakiseCo\Postgres\Driver\Pq\PqConnector::class,

                // connection pool configuration
                'poolMinActive' => 0,
                'poolMaxActive' => 2,
                'poolMaxIdleTime' => 30,
                'poolValidationInterval' => 15.0,
            ],
        ],
    ],

    'migrations' => [
        'table' => 'migrations',
        'namespace' => 'App\\Migrations',
        'directory' => dirname(__DIR__) . '/src/Migrations',
        'safe' => false,
    ],

    'orm' => [
        'entityPath' => [
            dirname(__DIR__) . '/tests/Entity'
        ],
    ],
];