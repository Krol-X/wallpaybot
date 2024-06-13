<?php

namespace App\Abstract\Service;

interface TelegramBotServiceInterface
{
    public function setToken(string $token): void;
    public function handleResponseData(array $data): void;
    public function getUpdates(): array;
}
