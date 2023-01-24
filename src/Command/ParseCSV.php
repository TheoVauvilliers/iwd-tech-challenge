<?php

namespace App\Command;

use App\Service\Connector\ConnectorShortcut;
use App\Service\Helper\Csv;
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
    // CSV => SHORCUT
    protected const MAPPING = [
        'description' => '',
        'status' => '',
        'epic' => '',
        'blocked by' => ''
    ];
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
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$output instanceof ConsoleOutputInterface) {
            throw new \LogicException('This command accepts only an instance of "ConsoleOutputInterface".');
        }

        $connector = new ConnectorShortcut();

        // Transform csv in array
        $csv = new Csv();
        $data = $csv->read($input->getArgument('csv-name'));
        var_dump($data);

        // Get epics and create an associative array
//        $epics = $connector->call('GET', 'epics');
//        $epics = array_reduce($epics, function ($epics, $epic) {
//            $epics[$epic['name']] = $epic['id'];
//            return $epics;
//        }, []);
//        var_dump($epics);

        // TODO: Insert data on shortcut App
        // Get project_id
//        $project = $connector->call('GET', 'projects');
//        $project_id = current($project)['id'];


//        $data = [
//            'name' => 'Story from CLI',
//            'project_id' => $project_id,
//        ];
//
//        $story = $connector->call('POST', 'stories', $data, 'json');
//        var_dump($story);

        return Command::SUCCESS;
    }
}
