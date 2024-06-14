<?php

namespace App\Service;

use App\Exception\TelegramApiException;
use App\Interface\Model\TelegramResponseInterface;
use App\Interface\Service\TelegramParseServiceInterface;
use App\Interface\Service\TelegramServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TelegramService implements TelegramServiceInterface
{
    private int $nextId = 0;

    public function __construct(
        private string                                 $telegramBotToken,
        private readonly HttpClientInterface           $httpClient,
        private readonly TelegramParseServiceInterface $parser,
        private readonly LoggerInterface               $logger
    )
    {
    }

    public function setToken(string $token): void
    {
        $this->telegramBotToken = $token;
    }

    public function getUpdates(int $limit = 100): array
    {
        // Добавляем $nextId в запрос
        $updates_data = $this->callTelegram('getUpdates',
            ['offset' => $this->nextId, 'limit' => $limit, 'timeout' => 0]
        );
        $events = $this->parser->parseUpdatesData($updates_data);
        $this->logger->notice('Events: ', $events);

        if (!empty($events)) {
            $lastEvent = end($events);
            $this->nextId = $lastEvent->getUpdateId() + 1;
        }

        return $events;
    }

    public function sendMessage(TelegramResponseInterface $data): array
    {
        return $this->callTelegram('sendMessage', $data->toArray());
    }

    public function callTelegram(string $method, ?array $data = null): array
    {
        $url = "https://api.telegram.org/bot{$this->telegramBotToken}/$method";

        $this->logger->notice('Calling Telegram: ' . $method, $data);
        $response = $this->httpClient->request('POST', $url, [
            'json' => $data
        ]);

        $response_data = $response->toArray();
        if ($response->getStatusCode() !== 200) {
            if (isset($response_data['description'])) {
                throw new TelegramApiException($response_data['description']);
            }
        }
        $this->logger->notice('Response: ', $response_data);
        return $response_data;
    }
}
