<?php

namespace App\Command\Shortcut;

use App\Service\Helper\ShortcutHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsCommand(
    name: 'iwd:shortcut:init-project',
    description: 'This command allows you to init a project...',
    aliases: ['iwd:s:ip', 'i:s:ip'],
    hidden: false
)]
class InitProject extends Command
{
    protected ?ShortcutHelper $shortcut = null;

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->shortcut = new ShortcutHelper();
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

        $data = [
            'name' => 'idw-challenge-project',
            'team_id' => '1'
        ];
        $project = $this->shortcut->create('projects', $data);

        return Command::SUCCESS;
    }
}
