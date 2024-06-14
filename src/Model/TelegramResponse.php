<?php

namespace App\Model;

use App\Interface\Model\TelegramResponseInterface;
use Psr\Log\LoggerInterface;

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

    public function withKeyboard(array $keyboard): self
    {
        $this->data['reply_markup'] = json_encode([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
        return $this;
    }

    public function withInlineKeyboard(array $keyboard): self
    {
        $this->data['reply_markup'] = json_encode([
            'inline_keyboard' => $keyboard
        ]);
        return $this;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
