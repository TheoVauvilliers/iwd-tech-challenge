<?php

namespace App\Command;

use App\Service\Connector\ConnectorShortcut;
use App\Service\Helper\Csv;
use App\Service\Helper\ShortcutHelper;
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
        'description' => 'name',
        'status' => 'workflow_state_id',
        'epic' => 'epic_id',
        'blocked by' => 'description' // TODO: CHANGE THIS !
    ];
    protected bool $requireFileName = true;

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

        // Transform csv in array
        $csv = new Csv();
        $rows = $csv->read($input->getArgument('csv-name'));
        $rows = $csv->headerAsAssocArray($rows);

        $shortcut = new ShortcutHelper();

        // Get projectId
        $projectId = current($shortcut->getProjects())['id'];

        // Get epics and create an associative array
        $epics = $shortcut->getEpics();
        $epics = $shortcut->dataToAssocArray($epics);

        // Get workflow, extract states and create an associative array
        $workfows = $shortcut->getWorkflows();
        $states = $shortcut->dataToAssocArray(current($workfows)['states']);

        // Generate an array containing all the information to push to the API
        $data = array_map(function ($row) use ($epics, $states, $projectId) {
            $row = array_combine(self::MAPPING, $row);

            foreach ($row as $key => $value) {
                // Epic name to epic id
                $row[$key] = $this->replaceNameById($key, $value, $row, $epics);
                // State name to State id
                $row[$key] = $this->replaceNameById($key, $value, $row, $states);
            }

            $row['project_id'] = $projectId;

            return $row;
        }, $rows);

        foreach ($data as $row) {
            try {
//                $story = $shortcut->pushStory($row);

                // TODO: Call to create Story-Links
                // https://developer.shortcut.com/api/rest/v3#Create-Story-Link

                $output->writeln([
                    'The story ' . $row['name'] . ' has been created',
                ]);
            } catch (\Throwable $t) {
                $output->writeln([
                    'Impossible to create this story : ' . $row['name'],
                    'Error : ' . $t->getMessage(),
                ]);
            }
        }

        return Command::SUCCESS;
    }

    protected function replaceNameById(string $key, string $value, array $row, array $data): string
    {
        // If the value exists on the application, returns the corresponding id, otherwise returns its own value
        if (in_array($value, array_keys($data))) {
            return $data[$value];
        }

        return $row[$key];
    }

    // TODO: Rearrange the stories in the correct order to be able to create the "is blocked by" relationships
}
