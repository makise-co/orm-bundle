<?php
/*
 * This file is part of the Makise-Co Framework
 *
 * World line: 0.571024a
 * (c) Dmitry K. <coder1994@gmail.com>
 */

declare(strict_types=1);

namespace MakiseCo\ORM\Console\Commands;

use MakiseCo\ApplicationInterface;
use MakiseCo\Console\Commands\CoroutineCommand;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\Migrator;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ConfirmationQuestion;

abstract class AbstractCommand extends CoroutineCommand
{
    protected ?Migrator $migrator = null;

    protected ?MigrationConfig $config = null;

    public function __construct(ApplicationInterface $app, Migrator $migrator, MigrationConfig $config)
    {
        $this->migrator = $migrator;
        $this->config = $config;

        parent::__construct($app);
    }

    /**
     * @return bool
     */
    protected function verifyConfigured(): bool
    {
        if (!$this->migrator->isConfigured()) {
            $this->writeln(
                "<fg=red>Migrations are not configured yet, run '<info>migrate:init</info>' first.</fg=red>"
            );

            return false;
        }

        return true;
    }

    /**
     * Check if current environment is safe to run migration.
     *
     * @return bool
     */
    protected function verifyEnvironment(): bool
    {
        if ($this->getOption('force') || $this->config->isSafe()) {
            //Safe to run
            return true;
        }

        $this->writeln('<fg=red>Confirmation is required to run migrations!</fg=red>');

        if (!$this->askConfirmation()) {
            $this->writeln('<comment>Cancelling operation...</comment>');

            return false;
        }

        return true;
    }

    protected function defineOptions(): void
    {
        $this->options[] = ['force', 's', InputOption::VALUE_NONE, 'Skip safe environment check'];

        parent::defineOptions();
    }

    /**
     * @return bool
     */
    protected function askConfirmation(): bool
    {
        $question = new QuestionHelper();

        return $question->ask(
            $this->input,
            $this->output,
            new ConfirmationQuestion('<question>Would you like to continue?</question> ')
        );
    }
}
