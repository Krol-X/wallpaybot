<?php

namespace App\Service;

use App\Exception\TelegramApiException;
use App\Interface\Model\TelegramResponseInterface;
use App\Interface\Service\TelegramServiceInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TelegramService implements TelegramServiceInterface
{
    public function __construct(
        private string                       $telegramBotToken,
        private readonly HttpClientInterface $httpClient
    )
    {
    }

    public function setToken(string $token): void
    {
        $this->telegramBotToken = $token;
    }

    public function getUpdates(): array
    {
        return $this->callTelegram('getUpdates');
    }

    public function sendMessage(TelegramResponseInterface $data): array
    {
        return $this->callTelegram('sendMessage', $data);
    }

    public function callTelegram(string $method, ?TelegramResponseInterface $data = null): array
    {
        $url = "https://api.telegram.org/bot{$this->telegramBotToken}/$method";

        $data = $data ? $data->toArray() : [];

        $response = $this->httpClient->request('POST', $url, [
            'json' => $data
        ]);

        $data = $response->toArray();
        if ($response->getStatusCode() !== 200) {
            if (isset($data['description'])) {
                throw new TelegramApiException($data['description']);
            }
        }
        return $data;
    }
}
