<?php

namespace App\Service;

use App\Interface\Message\TelegramEventMessageInterface;
use App\Interface\Service\TelegramParseServiceInterface;
use App\Message\TelegramEventMessage;

class TelegramParseService implements TelegramParseServiceInterface
{
    /**
     * @param array $data
     * @return TelegramEventMessageInterface[]
     */
    public function parseData(array $data): array
    {
        if (isset($data['ok']) && $data['ok']) {
            $data = $data['result'];
        }

        return array_map(fn($it) => new TelegramEventMessage($it), $data);
    }
}
