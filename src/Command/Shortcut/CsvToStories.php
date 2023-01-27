<?php

namespace App\Command\Shortcut;

use App\Utils\ShortcutUtils;
use App\Service\Helper\CsvHelper;
use App\Service\Helper\ShortcutHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
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
        'blocked by' => 'blocked_by'
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

        $progressBar = new ProgressBar($output, count($data));
        $output->writeln('Creating stories...');

        $stories = [];
        foreach ($data as $row) {
            $storyData = $row;
            unset($storyData['blocked_by']);

            try {
                $story = $this->shortcut->create('stories', $storyData);
                $stories[] = array_merge($row, $story);

                $progressBar->advance();
            } catch (\Throwable $t) {
                $output->writeln([
                    'Impossible to create this story : ' . $row['name'],
                    'Error : ' . $t->getMessage(),
                ]);
            }
        }

        $progressBar->finish();
        $output->writeln('');

        $storiesLinks = ShortcutUtils::generateStoryLinks($stories);

        $nbLinks = 0;
        foreach ($storiesLinks as $storyLinks) {
            $nbLinks += count($storyLinks['parents']);
        }

        $progressBar = new ProgressBar($output, $nbLinks);

        $output->writeln('Creating links...');

        foreach ($storiesLinks as $storyLinks) {
            foreach ($storyLinks['parents'] as $storyLink) {
                $data = [
                    'object_id' => $storyLinks['id'],
                    'subject_id' => $storyLink['id'],
                    'verb' => 'blocks'
                ];

                try {
                    $this->shortcut->create('story-links', $data);

                    $progressBar->advance();
                } catch (\Throwable $t) {
                    $output->writeln([
                        'Impossible to link for this story : ' . $storyLinks['id'],
                        'Parent : ' . $storyLink['id'],
                        'Error : ' . $t->getMessage(),
                    ]);
                }
            }
        }

        $progressBar->finish();
        $output->writeln('');

        return Command::SUCCESS;
    }

    // TODO: Rearrange the stories in the correct order to be able to create the "is blocked by" relationships
}
