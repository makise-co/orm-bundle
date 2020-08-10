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

class MigrateCommand extends AbstractCommand
{
    protected string $name = 'migrate';
    protected string $description = 'Run database migrations';

    protected array $options = [
        ['one', 'o', InputOption::VALUE_NONE, 'Execute only one (first) migration'],
    ];

    public function handle(): void
    {
        if (!$this->verifyConfigured() || !$this->verifyEnvironment()) {
            return;
        }

        $found = false;
        $count = $this->getOption('one') ? 1 : PHP_INT_MAX;

        while ($count > 0 && ($migration = $this->migrator->run())) {
            $found = true;
            $count--;

            $this->sprintf(
                "<info>Migration <comment>%s</comment> was successfully executed.</info>\n",
                $migration->getState()->getName()
            );
        }

        if (!$found) {
            $this->writeln('<fg=red>No outstanding migrations were found.</fg=red>');
        }
    }
}
