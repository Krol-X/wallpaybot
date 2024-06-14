<?php

namespace App\Interface\Model;

interface TelegramResponseInterface
{
    public function getChatId(): int;

    public function withData(array $data): self;

    public function withMessage(string $message): self;

    public function withKeyboard(array $keyboard): self;

    public function withInlineKeyboard(array $keyboard): self;

    public function toArray(): array;
}
