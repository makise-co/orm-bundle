<?php
/*
 * This file is part of the Makise-Co Framework
 *
 * World line: 0.571024a
 * (c) Dmitry K. <coder1994@gmail.com>
 */

declare(strict_types=1);

namespace MakiseCo\ORM\Console\Commands;

use MakiseCo\ORM\MigrationDeclaration;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Reactor\FileDeclaration;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeCommand extends AbstractCommand
{
    protected string $name = 'make:migration';
    protected string $description = 'Create new migration';

    protected array $arguments = [
        ['name', InputArgument::REQUIRED, 'Migration name'],
    ];

    protected array $options = [
        ['table', 't', InputOption::VALUE_OPTIONAL, 'Table to be created table'],
        [
            'field',
            'f',
            InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            'Create field in a format "name:type'
        ],
        ['comment', null, InputOption::VALUE_OPTIONAL, 'Table to be created table'],
    ];

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        // migrator is not needed, only initializing migrator config
        $this->config = $this->makise->getContainer()->get(MigrationConfig::class);
    }

    public function handle(): void
    {
        $declaration = new MigrationDeclaration(
            $this->input->getArgument('name'),
            ''
        );

        if (!empty($this->input->getOption('table'))) {
            $fields = [];
            foreach ($this->input->getOption('field') as $field) {
                if (strpos($field, ':') === false) {
                    throw new \InvalidArgumentException("Field definition must in 'name:type' form");
                }

                [$name, $type] = explode(':', $field);
                $fields[$name] = $type;
            }

            $declaration->declareCreation((string)$this->input->getOption('table'), $fields);
        }

        $file = new FileDeclaration($this->config->getNamespace());
        $file->setDirectives('strict_types=1');
//        $file->setComment("");
        $file->addElement($declaration);

        $filename = $this->migrator->getRepository()->registerMigration(
            (string)$this->input->getArgument('name'),
            $declaration->getName(),
            $file->render()
        );

        $this->sprintf(
            "Declaration of '<info>%s</info>' has been successfully written into '<comment>%s</comment>'.\n",
            $declaration->getName(),
            $filename
        );
    }
}
