<?php

namespace App\Command;

use React\EventLoop\Loop;
use React\EventLoop\TimerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'bot:long-pool',
    description: 'Polls Telegram for updates',
)]
class BotLongPoolCommand extends Command
{
    private HttpClientInterface $client;
    private string $token;
    private int $offset = 0;

    public function __construct(HttpClientInterface $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('token', InputArgument::OPTIONAL, 'Telegram Bot Token');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->token = $input->getArgument('token') ?? getenv('TELEGRAM_BOT_TOKEN');

        if (!$this->token) {
            throw new \RuntimeException('Telegram bot token not provided. Set it via an argument or the TELEGRAM_BOT_TOKEN environment variable.');
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting Telegram Bot polling...');
        $loop = Loop::get();
        $loop->addPeriodicTimer(1, function (TimerInterface $timer) use ($output) {
            $this->pollUpdates($output);
        });

        $loop->run();
        return Command::SUCCESS;
    }

    private function pollUpdates(OutputInterface $output)
    {
        $url = sprintf('https://api.telegram.org/bot%s/getUpdates', $this->token);
        $response = $this->client->request('GET', $url, [
            'query' => [
                'offset' => $this->offset,
                'timeout' => 10,
            ],
        ]);

        $data = $response->toArray();

        if (isset($data['result']) && is_array($data['result'])) {
            foreach ($data['result'] as $update) {
                $this->offset = $update['update_id'] + 1;
                $this->handleUpdate($update, $output);
            }
        }
    }

    private function handleUpdate(array $update, OutputInterface $output): void
    {
        if (isset($update['message']['text'])) {
            $messageText = $update['message']['text'];
            $chatId = $update['message']['chat']['id'];
            $output->writeln(sprintf('Received message: %s', $messageText));
            $this->sendMessage($chatId, 'Message received: ' . $messageText);
        }
    }

    private function sendMessage(int $chatId, string $text): void
    {
        $url = sprintf('https://api.telegram.org/bot%s/sendMessage', $this->token);
        $this->client->request('POST', $url, [
            'json' => [
                'chat_id' => $chatId,
                'text' => $text
            ]
        ]);
    }
}
