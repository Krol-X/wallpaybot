<?php

namespace App\Message;

use App\Interface\Message\TelegramEventMessageInterface;
use App\Interface\Model\TelegramResponseInterface;
use App\Model\TelegramResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class TelegramEventMessage implements TelegramEventMessageInterface
{
    private array $content;
    private array $data;
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger,
        array $content
    )
    {
        $this->logger = $logger;
        $logger->notice('Content: ', $content);
        $this->content = $content;
        $this->data = $content['callback_query'] ?? $content['message'] ?? $content['my_chat_member'] ?? [];
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getChatId(): int
    {
        return $this->data['chat']['id'] ?? 0;
    }

    public function getMessageId(): int
    {
        return $this->data['message_id'] ?? 0;
    }

    public function getUpdateId(): int
    {
        return $this->content['update_id'] ?? 0;
    }

    public function getText(): string
    {
        return $this->data['text'] ?? $this->data['data'] ?? '';
    }

    public function isQuery(): bool
    {
        return isset($content['callback_query']);
    }

    public function send(MessageBusInterface $bus): void
    {
        $chatId = $this->getChatId();
        if ($chatId === 0) {
            $this->logger->warning('Cannot send message: chat ID is missing');
            return;
        }
        $bus->dispatch($this);
    }

    public function newResponse(string $text): TelegramResponseInterface
    {
        $response = new TelegramResponse($this->getChatId());
        return $response->withMessage($text);
    }
}
