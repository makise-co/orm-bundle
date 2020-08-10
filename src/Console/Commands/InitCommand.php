<?php
/*
 * This file is part of the Makise-Co Framework
 *
 * World line: 0.571024a
 * (c) Dmitry K. <coder1994@gmail.com>
 */

declare(strict_types=1);

namespace MakiseCo\ORM\Console\Commands;

class InitCommand extends AbstractCommand
{
    protected string $name = 'migrate:init';
    protected string $description = 'Init migrations component (create migrations table)';

    public function handle(): void
    {
        $this->migrator->configure();
        $this->info('Migrations table were successfully created');
    }
}
