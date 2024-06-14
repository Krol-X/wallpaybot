<?php

namespace App\Interface\Model;

interface TelegramResponseInterface
{
    public function getChatId(): int;

    public function withData(array $data): self;

    public function withReplyMarkup($data): self;

    public function toArray(): array;
}
