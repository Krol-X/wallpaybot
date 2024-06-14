<?php

namespace App\Message;

use App\Interface\Message\TelegramEventMessageInterface;
use App\Interface\Model\TelegramResponseInterface;
use App\Model\TelegramResponse;
use Symfony\Component\Messenger\MessageBusInterface;

/*
{
    "update_id": 702982016,
    "message": {
      "message_id": 87,
      "from": {
        "id": 1868566649,
        "is_bot": false,
        "first_name": "Alex",
        "last_name": "ᏦᎮᎧᏗ",
        "username": "Krol_X",
        "language_code": "ru",
        "is_premium": true
      },
      "chat": {
        "id": 1868566649,
        "first_name": "Alex",
        "last_name": "ᏦᎮᎧᏗ",
        "username": "Krol_X",
        "type": "private"
      },
      "date": 1718289606,
      "text": "123"
    }
  }
*/

class TelegramEventMessage implements TelegramEventMessageInterface
{
    private array $content;
    private array $data;

    public function __construct(array $content)
    {
        $this->content = $content;
        $this->data = $content['callback_query'] ?? $content['message'];
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
        return $this->data['chat']['id'];
    }

    public function getMessageId(): int
    {
        return $this->data['message_id'];
    }

    public function getUpdateId(): int
    {
        return $this->content['update_id'];
    }

    public function getText(): string
    {
        return $this->data['text'] ?? $this->data['data'];
    }

    public function isQuery(): bool
    {
        return isset($content['callback_query']);
    }

    public function send(MessageBusInterface $bus): void
    {
        $bus->dispatch($this);
    }

    public function newResponseMessage(string $text): TelegramResponseInterface
    {
        return new TelegramResponse($this->getChatId());
    }
}
