<?php

namespace App\Model;

use App\Interface\Model\TelegramResponseInterface;

class TelegramResponse implements TelegramResponseInterface
{
    private array $data = [];

    public function __construct(
        int $chat_id
    )
    {
        $this->data['chat_id'] = $chat_id;
    }

    public function getChatId(): int
    {
        return $this->data['chat_id'];
    }

    public function withData(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function withMessage(string $message): self
    {
        $this->data['text'] = $message;
        return $this;
    }

    public function withReplyMarkup($data): self
    {
        if ($data) {
            $this->data['reply_markup'] = $data->toArray();
        } else {
            unset($this->data['reply_markup']);
        }
        return $this;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
