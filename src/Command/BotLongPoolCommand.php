<?php

namespace App\Command;

use App\Interface\Service\TelegramServiceInterface;
use Psr\Log\LoggerInterface;
use React\EventLoop\Loop;
use React\EventLoop\TimerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'bot:long-pool',
    description: 'Polls Telegram for updates',
)]
class BotLongPoolCommand extends Command
{
    private int $offset = 0;

    public function __construct(
        private readonly TelegramServiceInterface $telegram,
        private readonly MessageBusInterface      $bus,
        private readonly LoggerInterface          $logger
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
            $this->telegram->setToken($token);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting telegram long-pool loop...');
        $loop = Loop::get();
        $loop->addPeriodicTimer(1, function (TimerInterface $timer) {
            try {
                $events = $this->telegram->getUpdates();

                foreach ($events as $event) {
                    $event->send($this->bus);
                }
            } catch (\Throwable $e) {
                $this->logger->critical(sprintf('Error in long-pooling: %s', $e->getMessage()), ['exception' => $e]);
            }
        });
        $loop->run();
        return Command::SUCCESS;
    }
}
