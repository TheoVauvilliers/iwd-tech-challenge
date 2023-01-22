<?php

namespace App\Commands;

use App\Connectors\ConnectorShortcut;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

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
                'csv-name',
                $this->requireFileName ? InputArgument::REQUIRED : InputArgument::OPTIONAL,
                'The name of the csv file in the public/uploads/ directory'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$output instanceof ConsoleOutputInterface) {
            throw new \LogicException('This command accepts only an instance of "ConsoleOutputInterface".');
        }

        // TODO: Init the connector
        $connector = new ConnectorShortcut();

        // TODO: Load csv file

        // TODO: Push data with the connector
        $epics = $connector->call('GET', 'epics');

        $output->writeln(array_map(function ($data) {
            return 'EPIC Name : ' . $data["name"];
        }, $epics));

        return Command::SUCCESS;
    }
}
