<?php

namespace App\Service;

use App\Interface\Message\TelegramEventMessageInterface;
use App\Interface\Service\TelegramParseServiceInterface;
use App\Message\TelegramEventMessage;
use Psr\Log\LoggerInterface;

class TelegramParseService implements TelegramParseServiceInterface
{
    public function __construct(
        private readonly LoggerInterface $logger
    )
    {
    }

    /**
     * @param array $data
     * @return TelegramEventMessageInterface[]
     */
    public function parseUpdatesData(array $data): array
    {
        $result = $data['result'] ?? [$data];
        if (count($result) === 0)
            return [];

        return array_map(function ($it) {
            try {
                return new TelegramEventMessage($it);
            } catch (\Throwable $e) {
                $this->logger->error(sprintf('Error parsing update: %s', $e->getMessage()));
                return null;
            }
        }, $result);
    }
}
