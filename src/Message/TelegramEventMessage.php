<?php

namespace App\Message;

use App\Interface\Message\TelegramEventMessageInterface;
use App\Interface\Model\TelegramResponseInterface;
use App\Model\TelegramResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class TelegramEventMessage implements TelegramEventMessageInterface
{
    private array $content;
    private array $data;
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger,
        array           $content
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

    public function getFromId(): int
    {
        return $this->data['from']['id'] ?? 0;
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
        return isset($this->content['callback_query']);
    }

    public function send(MessageBusInterface $bus, ?int $delay = null): void
    {
        $chatId = $this->getFromId();
        if ($chatId === 0) {
            $this->logger->warning('Cannot send message: chat ID is missing');
            return;
        }

        if ($delay) {
            $bus->dispatch($this)->with(new DelayStamp($delay));
        } else {
            $bus->dispatch($this);
        }
    }

    public function newResponse(string $text): TelegramResponseInterface
    {
        $response = new TelegramResponse($this->getFromId());
        return $response->withMessage($text);
    }
}
