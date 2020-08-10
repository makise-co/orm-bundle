<?php
/*
 * This file is part of the Makise-Co Framework
 *
 * World line: 0.571024a
 * (c) Dmitry K. <coder1994@gmail.com>
 */

declare(strict_types=1);

namespace MakiseCo\ORM\Console\Commands;

use Spiral\Migrations\State;

class StatusCommand extends AbstractCommand
{
    protected const PENDING = '<fg=red>not executed yet</fg=red>';

    protected string $name = 'migrate:status';
    protected string $description = 'Get list of all available migrations and their statuses';

    public function handle(): void
    {
        if (!$this->verifyConfigured()) {
            return;
        }

        if (empty($this->migrator->getMigrations())) {
            $this->writeln('<comment>No migrations were found.</comment>');

            return;
        }

        $table = $this->makeTable(['Migration', 'Created at', 'Executed at']);

        foreach ($this->migrator->getMigrations() as $migration) {
            $state = $migration->getState();

            $table->addRow(
                [
                    $state->getName(),
                    $state->getTimeCreated()->format('Y-m-d H:i:s'),
                    $state->getStatus() === State::STATUS_PENDING
                        ? self::PENDING
                        : '<info>' . $state->getTimeExecuted()->format('Y-m-d H:i:s') . '</info>'
                ]
            );
        }

        $table->render();
    }
}
