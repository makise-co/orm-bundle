<?php
/*
 * This file is part of the Makise-Co Framework
 *
 * World line: 0.571024a
 * (c) Dmitry K. <coder1994@gmail.com>
 */

declare(strict_types=1);

namespace MakiseCo\ORM;

use Cycle\Annotated;
use Cycle\ORM;
use Cycle\Schema;
use DI\Container;
use MakiseCo\Config\ConfigRepositoryInterface;
use MakiseCo\Http\Events\WorkerExit;
use MakiseCo\Http\Events\WorkerStarted;
use MakiseCo\Providers\ServiceProviderInterface;
use Spiral\Database\Config\DatabaseConfig;
use Spiral\Database\DatabaseInterface;
use Spiral\Database\DatabaseManager;
use Spiral\Database\DatabaseProviderInterface;
use Spiral\Migrations;
use Spiral\Tokenizer;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ORMProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->get(EventDispatcher::class)->addListener(WorkerStarted::class, function () use ($container) {
            // initialize ORM at service start to prevent concurrent initialization
            $container->get(ORM\ORMInterface::class);
        });

        $container->get(EventDispatcher::class)->addListener(WorkerExit::class, function () use ($container) {
            // close connection pools when worker should stop
            foreach ($container->get(DatabaseManager::class)->getDatabases() as $database) {
                $database->getDriver(DatabaseInterface::READ)->disconnect();
                $database->getDriver(DatabaseInterface::WRITE)->disconnect();
            }
        });

        // alias DatabaseProviderInterface to its implementation
        $container->set(DatabaseProviderInterface::class, fn() => $container->get(DatabaseManager::class));

        $container->set(
            DatabaseManager::class,
            static function (ConfigRepositoryInterface $config) {
                return new DatabaseManager(
                    new DatabaseConfig($config->get('database'))
                );
            }
        );

        // migrator
        $container->set(
            Migrations\Migrator::class,
            static function (DatabaseManager $dbal, ConfigRepositoryInterface $config) {
                $migrationConfig = new Migrations\Config\MigrationConfig(
                    $config->get('database.migrations', [])
                );

                return new Migrations\Migrator(
                    $migrationConfig,
                    $dbal,
                    new Migrations\FileRepository($migrationConfig)
                );
            }
        );

        // alias ORMInterface to its implementation
        $container->set(ORM\ORMInterface::class, fn() => $container->get(ORM\ORM::class));

        $container->set(
            ORM\ORM::class,
            static function (Container $container, DatabaseManager $dbal, ConfigRepositoryInterface $config) {
                // Class locator
                $cl = (new Tokenizer\Tokenizer(
                    new Tokenizer\Config\TokenizerConfig(
                        [
                            'directories' => $config->get('database.orm.entityPath', []),
                            'exclude' => $config->get('database.orm.entityExclude', [])
                        ]
                    )
                ))->classLocator();

                $schema = (new Schema\Compiler())->compile(
                    new Schema\Registry($dbal),
                    [
                        new Annotated\Embeddings($cl),            // register annotated embeddings
                        new Annotated\Entities($cl),              // register annotated entities
                        new Schema\Generator\ResetTables(),       // re-declared table schemas (remove columns)
                        new Annotated\MergeColumns(),             // register non field columns (table level)
                        new Schema\Generator\GenerateRelations(), // generate entity relations
                        new Schema\Generator\ValidateEntities(),  // make sure all entity schemas are correct
                        new Schema\Generator\RenderTables(),      // declare table schemas
                        new Schema\Generator\RenderRelations(),   // declare relation keys and indexes
                        new Annotated\MergeIndexes(),             // register non entity indexes (table level)
                        new Schema\Generator\GenerateTypecast(),  // typecast non string columns
                    ]
                );

                $orm = new ORM\ORM(
                    new ORM\Factory($dbal, null, null, null),
                    new ORM\Schema($schema)
                );

                $proxyFactory = $container->make(\Cycle\ORM\Promise\ProxyFactory::class);
                $orm = $orm->withPromiseFactory($proxyFactory);

                // enable coroutine safe heap
                if ((bool)$config->get('database.orm.enableCoroutineHeap', true)) {
                    $orm = $orm->withHeap(new CoroutineHeap());
                }

                return $orm;
            }
        );
    }
}
