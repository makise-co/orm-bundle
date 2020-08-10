<?php
/*
 * This file is part of the Makise-Co Framework
 *
 * World line: 0.571024a
 * (c) Dmitry K. <coder1994@gmail.com>
 */

declare(strict_types=1);

namespace MakiseCo\ORM\Console\Commands;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

class ReplayCommand extends AbstractCommand
{
    protected string $name = 'migrate:replay';
    protected string $description = 'Replay (down, up) one or multiple migrations';

    protected array $options = [
        ['all', 'a', InputOption::VALUE_NONE, 'Replay all migrations'],
    ];

    public function handle(): void
    {
        if (!$this->verifyEnvironment()) {
            //Making sure we can safely migrate in this environment
            return;
        }

        $rollback = ['--force' => true];
        $migrate = ['--force' => true];

        if ($this->getOption('all')) {
            $rollback['--all'] = true;
        } else {
            $migrate['--one'] = true;
        }

        $this->writeln('Rolling back executed migration(s)...');

        $cmd = $this->getApplication()->find('migrate:rollback');
        $cmd->run(new ArrayInput($rollback), $this->output);

        $this->writeln('');

        $this->writeln('Executing outstanding migration(s)...');

        $cmd = $this->getApplication()->find('migrate');
        $cmd->run(new ArrayInput($migrate), $this->output);
    }
}
