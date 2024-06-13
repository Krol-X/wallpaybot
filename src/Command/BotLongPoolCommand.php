<?php

namespace App\Command;

use App\Service\AppBotServiceInterface;
use React\EventLoop\Loop;
use React\EventLoop\TimerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'bot:long-pool',
    description: 'Polls Telegram for updates',
)]
class BotLongPoolCommand extends Command
{
    private int $offset = 0;

    public function __construct(
        private readonly AppBotServiceInterface $appBotService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('token', InputArgument::OPTIONAL, 'Telegram Bot Token');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $token = $input->getArgument('token');
        if ($token) {
            $this->appBotService->setToken($token);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting telegram long-pool loop...');
        $loop = Loop::get();
        $loop->addPeriodicTimer(1, function (TimerInterface $timer) use ($output) {
            $updates_data = $this->appBotService->getUpdates();
            $this->appBotService->handleResponseData($updates_data);
        });
        $loop->run();
        return Command::SUCCESS;
    }
}
