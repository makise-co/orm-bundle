<?php
/*
 * This file is part of the Makise-Co Framework
 *
 * World line: 0.571024a
 * (c) Dmitry K. <coder1994@gmail.com>
 */

declare(strict_types=1);

namespace MakiseCo\ORM\Tests\Testing;

use DI\Container;
use MakiseCo\Application;
use MakiseCo\ORM\Testing\DatabaseTransactions;
use MakiseCo\Testing\CoroutineTestCase;
use Spiral\Database\DatabaseManager;

class DatabaseTransactionsTest extends CoroutineTestCase
{
    use DatabaseTransactions;

    protected array $connectionsToTransact = ['default'];

    protected Application $app;
    protected Container $container;

    protected int $txId;

    protected function setUp(): void
    {
        $this->app = new Application(
            dirname(__DIR__ . '/../'),
            dirname(__DIR__) . '/../tests/config/'
        );

        $this->container = $this->app->getContainer();

        $this->bootDatabaseTransactions();

        /** @var DatabaseManager $dbal */
        $dbal = $this->container->get(DatabaseManager::class);
        $this->txId = $dbal
            ->database('default')
            ->query('SELECT txid_current();')
            ->fetchAll()[0]['txid_current'];
    }

    protected function tearDown(): void
    {
        $this->cleanupDatabaseTransactions();
        /** @var DatabaseManager $dbal */
        $dbal = $this->container->get(DatabaseManager::class);
        $dbal->database('default')->getDriver()->disconnect();
    }

    public function testTransaction(): void
    {
        /** @var DatabaseManager $dbal */
        $dbal = $this->container->get(DatabaseManager::class);
        $txId = $dbal
            ->database('default')
            ->query('SELECT txid_current();')
            ->fetchAll()[0]['txid_current'];

        self::assertEquals($this->txId, $txId);
    }
}
