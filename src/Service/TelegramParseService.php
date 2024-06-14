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
        if (isset($data['ok']) && $data['ok']) {
            $data = $data['result'];
        }
        $this->logger->notice('> ', $data);

        return array_map(function ($it) {
            try {
                return new TelegramEventMessage($this->logger, $it);
            } catch (\Throwable $e) {
                $this->logger->error(sprintf('Error parsing update: %s', $e->getMessage()));
                return null;
            }
        }, $data);
    }
}
