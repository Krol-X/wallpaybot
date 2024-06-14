<?php

namespace App\Interface\Service;

use App\Interface\Message\TelegramEventMessageInterface;

interface TelegramParseServiceInterface
{
    /**
     * @param array $data
     * @return TelegramEventMessageInterface[]
     */
    public function parseUpdatesData(array $data): array;
}
