<?php

namespace App\Abstract\Service;

use App\Attribute\OnTelegramMessage;
use App\Attribute\OnTelegramQuery;
use App\Exception\InvalidTelegramDataException;
use App\Exception\MissingChatIdException;
use App\Exception\TelegramApiException;
use App\Utils\ArrayUtils;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class TelegramBotService implements TelegramBotServiceInterface
{
    public function __construct(
        private string                       $telegramBotToken,
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface     $logger
    )
    {
    }

    public function setToken(string $token): void
    {
        $this->telegramBotToken = $token;
    }

    /**
     * @throws MissingChatIdException
     * @throws InvalidTelegramDataException
     */
    public function handleResponseData(array $data): void
    {
        $this->logger->notice('Received data from Telegram:', $data);
        if (!$data) {
            throw new InvalidTelegramDataException('Invalid data received from Telegram');
        }
        if (count($data) == 0)
            return;
        if (ArrayUtils::isAssociative($data)) {
            // $data['ok'] == true
            $this->processEvent($data);
        } else {
            foreach ($data as $event) {
                $this->processEvent($event);
            }
        }
    }

    /**
     * @throws MissingChatIdException
     */
    private function processEvent(array $event): void
    {
        // todo...
        $this->logger->notice('Event: ', $event);
        $data = $event['callback_query'] ?? $event['message'];
        $text = mb_strtolower($data['text'] ?? $data['data'] ?? 'unknown', 'UTF-8');
        $chatId = $data['chat']['id'] ?? null;

        if (!$chatId) {
            throw new MissingChatIdException('Chat ID is missing in the received data');
        }

        $reflection = new ReflectionClass($this);
        foreach ($reflection->getMethods() as $method) {
            foreach ($method->getAttributes() as $attribute) {
                $attrInstance = $attribute->newInstance();

                if ($this->checkAttribute($attrInstance, $text)) {
                    if ($this->{$method->getName()}($data, $chatId, $event))
                        break;
                }
            }
        }

        $this->defaultAction($data, $chatId, $event);
    }

    private function checkAttribute($attribute, string $text): bool
    {
        if ($attribute instanceof OnTelegramMessage) {
            if ($attribute->command && $attribute->command === $text) {
                return true;
            }
            if ($attribute->pattern && preg_match($attribute->pattern, $text)) {
                return true;
            }
        }
        if ($attribute instanceof OnTelegramQuery) {
            if ($attribute->command && $attribute->command === $text) {
                return true;
            }
            if ($attribute->pattern && preg_match($attribute->pattern, $text)) {
                return true;
            }
        }
        return false;
    }

    abstract function defaultAction($data, $chatId, $event): void;

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TelegramApiException
     */
    protected function SendMessage($chatId, $text, $keyboard = null): void
    {
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
        ];
        if ($keyboard) {
            $data['reply_markup'] = json_encode($keyboard);
        }

        $this->ExecuteTelegram('sendMessage', $data);
    }

    protected function WithKeyboard(array $keyboard): array
    {
        return [
            'resize_keyboard' => true,
            'keyboard' => $keyboard
        ];
    }

    protected function WithInlineKeyboard(array $keyboard): array
    {
        return [
            'inline_keyboard' => $keyboard
        ];
    }

    /**
     * @throws TransportExceptionInterface
     * @throws TelegramApiException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getUpdates(): array
    {
        $response = $this->ExecuteTelegram('getUpdates', []);
        return $response['result'];
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TelegramApiException
     */
    protected function ExecuteTelegram($command, $json_data): array
    {
        $url = "https://api.telegram.org/bot{$this->telegramBotToken}/$command";

        $response = $this->httpClient->request('POST', $url, [
            'json' => $json_data
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
