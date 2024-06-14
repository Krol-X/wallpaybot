<?php

namespace App\Interface\Service;

use App\Interface\Message\TelegramEventMessageInterface;

interface TelegramParseServiceInterface
{
    /**
     * @param array $data
     * @return TelegramEventMessageInterface[]
     */
    public function parseData(array $data): array;
}
