<?php
/*
 * This file is part of the Makise-Co Framework
 *
 * World line: 0.571024a
 * (c) Dmitry K. <coder1994@gmail.com>
 */

declare(strict_types=1);

namespace MakiseCo\ORM\Tests;

use Cycle\ORM\ORM;
use Cycle\ORM\Transaction;
use DI\Container;
use MakiseCo\Config\ConfigRepositoryInterface;
use MakiseCo\Config\Repository;
use MakiseCo\ORM\ORMProvider;
use MakiseCo\ORM\Tests\Entity\User;
use Spiral\Database\DatabaseInterface;
use Spiral\Database\DatabaseManager;
use Swoole\Coroutine;

class BundleTest extends CoroTestCase
{
    private Container $container;

    private DatabaseManager $dbal;

    protected function setUp(): void
    {
        $this->container = $container = new Container();
        $provider = new ORMProvider();

        $config = new Repository();
        $config['database'] = require __DIR__ . '/database.php';

        $container->set(ConfigRepositoryInterface::class, $config);

        $provider->register($container);

        $this->dbal = $container->get(DatabaseManager::class);

        $database = $this->dbal->database('default');

        // run tests inside transaction
        $database->begin();

        $this->createSchema($database);
    }

    protected function tearDown(): void
    {
        $this->dbal->database('default')->rollback();
        $this->dbal->database('default')->getDriver()->disconnect();
    }

    protected function createSchema(DatabaseInterface $database): \Spiral\Database\Schema\AbstractTable
    {
        $schema = $database->table('users')->getSchema();
        $schema->primary('id');
        $schema->string('name', 64);
        $schema->integer('manager_id');
        $schema->foreignKey(['manager_id'])->references('users', ['id']);
        $schema->save();

        return $schema;
    }

    public function testOrmWorks(): void
    {
        $orm = $this->container->get(ORM::class);
        $repo = $orm->getRepository(User::class);

        $rootUser = new User();
        $rootUser->name = 'Root';

        $transaction = new Transaction($orm);
        $transaction->persist($rootUser);
        $transaction->run();

        self::assertSame(1, $rootUser->id);

        $subUser = new User();
        $subUser->name = 'Sub';
        $subUser->manager = $rootUser;

        $transaction->persist($subUser);
        $transaction->run();

        self::assertSame(2, $subUser->id);

        /** @var User $foundSub */
        $foundSub = $repo
            ->select()
//            ->load('manager')
            ->wherePK($subUser->id)
            ->fetchOne();

        self::assertSame($subUser->name, $foundSub->name);
        self::assertSame($foundSub->manager->id, $rootUser->id);
    }

    public function testCoroutineHeap(): void
    {
        $orm = $this->container->get(ORM::class);

        $rootUser = new User();
        $rootUser->name = 'Root';

        $transaction = new Transaction($orm);
        $transaction->persist($rootUser);
        $transaction->run();

        self::assertSame(1, $rootUser->id);

        $ch = new Coroutine\Channel(1);

        Coroutine::create(function () use ($ch, $orm, $rootUser) {
            try {
                // heap should not be shared with child coroutine
                self::assertFalse($orm->getHeap()->has($rootUser));
                $ch->push(1);
            } catch (\Throwable $e) {
                $ch->push($e);
            }
        });

        $res = $ch->pop();
        if ($res instanceof \Throwable) {
            throw $res;
        }
    }
}