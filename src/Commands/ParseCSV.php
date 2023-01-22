<?php

namespace App\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'iwd:parse-csv',
    description: 'This command allows you to parse a csv file...',
    hidden: false
)]
class ParseCSV extends Command
{
    protected bool $requireFileName = true;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'csv-path',
                $this->requireFileName ? InputArgument::REQUIRED : InputArgument::OPTIONAL,
                'The path from your location to the targeted csv'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$output instanceof ConsoleOutputInterface) {
            throw new \LogicException('This command accepts only an instance of "ConsoleOutputInterface".');
        }

        // TODO: Init the connector for Shortcut
        // TODO: Load csv file
        // TODO: Push data with the connector

        $output->writeln([
            'Parse CSV',
            '============',
            $output->writeln('CSV Path : ' . $input->getArgument('csv-path'))
        ]);

        return Command::SUCCESS;
    }
}
