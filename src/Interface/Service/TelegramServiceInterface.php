<?php

namespace App\Interface\Service;

use App\Interface\Model\TelegramResponseInterface;

interface TelegramServiceInterface
{
    public function setToken(string $token): void;

    public function getUpdates(): array;

    public function sendMessage(TelegramResponseInterface $data): array;

    public function callTelegram(string $method, ?array $data = null): array;
}
