<?php
/**
 * This file is part of the Makise-Co Framework
 *
 * World line: 0.571024a
 * (c) Dmitry K. <coder1994@gmail.com>
 */

declare(strict_types=1);

return [
    'name' => 'makise-co',

    'providers' => [
        \MakiseCo\Log\LoggerServiceProvider::class,
        \MakiseCo\Event\EventDispatcherServiceProvider::class,
        \MakiseCo\Console\ConsoleServiceProvider::class,
        \MakiseCo\ORM\ORMProvider::class,
    ],

    'commands' => [
        \MakiseCo\ORM\Console\Commands\MakeCommand::class,
        \MakiseCo\ORM\Console\Commands\MigrateCommand::class,
        \MakiseCo\ORM\Console\Commands\ReplayCommand::class,
        \MakiseCo\ORM\Console\Commands\RollbackCommand::class,
        \MakiseCo\ORM\Console\Commands\StatusCommand::class,
    ],
];
