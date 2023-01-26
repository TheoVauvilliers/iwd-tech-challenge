<?php

namespace App\Command\Shortcut;

use App\Utils\ShortcutUtils;
use App\Service\Helper\CsvHelper;
use App\Service\Helper\ShortcutHelper;
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
    name: 'iwd:shortcut:csv-to-stories',
    description: 'This command allows you to parse a csv file...',
    aliases: ['iwd:s:cs', 'i:s:cs'],
    hidden: false
)]
class CsvToStories extends Command
{
    // CSV => SHORCUT
    protected const MAPPING = [
        'description' => 'name',
        'status' => 'workflow_state_id',
        'epic' => 'epic_id',
        'blocked by' => 'description' // TODO: CHANGE THIS !
    ];
    protected bool $requireFileName = true;
    protected ?CsvHelper $csv = null;
    protected ?ShortcutHelper $shortcut = null;

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->csv = new CsvHelper();
        $this->shortcut = new ShortcutHelper();
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

        // Transform csv in array
        $rows = $this->csv->read($input->getArgument('csv-name'));
        $rows = $this->csv->headerAsAssocArray($rows);

        // Get projectId
        $projectId = current($this->shortcut->getProjects())['id'];

        // Get epics and create an associative array
        $epics = $this->shortcut->getEpics();
        $epics = $this->shortcut->dataToAssocArray($epics);

        // Get workflow, extract states and create an associative array
        $workfows = $this->shortcut->getWorkflows();
        $states = $this->shortcut->dataToAssocArray(current($workfows)['states']);

        // Generate an array containing all the information to push to the API
        $data = ShortcutUtils::generatePostDataStories($rows, [$states, $epics], $projectId, self::MAPPING);

        foreach ($data as $row) {
            try {
//                $story = $this->shortcut->push('stories', $row);

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
