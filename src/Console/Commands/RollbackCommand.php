<?php
/*
 * This file is part of the Makise-Co Framework
 *
 * World line: 0.571024a
 * (c) Dmitry K. <coder1994@gmail.com>
 */

declare(strict_types=1);

namespace MakiseCo\ORM\Console\Commands;

use Symfony\Component\Console\Input\InputOption;

class RollbackCommand extends AbstractCommand
{
    protected string $name = 'migrate:rollback';
    protected string $description = 'Rollback one (default) or multiple migrations';

    protected array $options = [
        ['all', 'a', InputOption::VALUE_NONE, 'Rollback all executed migrations'],
    ];

    public function handle(): void
    {
        if (!$this->verifyConfigured() || !$this->verifyEnvironment()) {
            //Making sure we can safely migrate in this environment
            return;
        }

        $found = false;
        $count = !$this->getOption('all') ? 1 : PHP_INT_MAX;
        while ($count > 0 && ($migration = $this->migrator->rollback())) {
            $found = true;
            $count--;
            $this->sprintf(
                "<info>Migration <comment>%s</comment> was successfully rolled back.</info>\n",
                $migration->getState()->getName()
            );
        }

        if (!$found) {
            $this->writeln('<fg=red>No executed migrations were found.</fg=red>');
        }
    }
}
