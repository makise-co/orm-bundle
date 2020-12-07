<?php
/*
 * This file is part of the Makise-Co Framework
 *
 * World line: 0.571024a
 * (c) Dmitry K. <coder1994@gmail.com>
 */

declare(strict_types=1);

namespace MakiseCo\ORM\Testing;

use Spiral\Database\DatabaseManager;

trait DatabaseTransactions
{
    protected function bootDatabaseTransactions(): void
    {
        /* @var DatabaseManager $db */
        $db = $this->container->get(DatabaseManager::class);

        foreach ($this->connectionsToTransact() as $connection) {
            $db->database($connection)->begin();
        }
    }

    protected function cleanupDatabaseTransactions(): void
    {
        /* @var DatabaseManager $db */
        $db = $this->container->get(DatabaseManager::class);

        foreach ($this->connectionsToTransact() as $connection) {
            try {
                $db->database($connection)->rollback();
            } catch (\Throwable $e) {
                $this->addWarning("Unable to ROLLBACK transaction on \"{$connection}\" connection: {$e->getMessage()}");
            }
        }
    }

    /**
     * The database connections that should have transactions.
     *
     * @return string[]
     */
    protected function connectionsToTransact(): array
    {
        return property_exists($this, 'connectionsToTransact') ? $this->connectionsToTransact : [];
    }
}
