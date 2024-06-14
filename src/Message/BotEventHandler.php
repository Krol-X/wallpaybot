<?php

namespace App\Message;

use App\Abstract\Message\TelegramEventHandler;
use App\Interface\Message\TelegramEventMessageInterface;
use App\Interface\Service\TelegramServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class BotEventHandler extends TelegramEventHandler
{
    public function __construct(
        private readonly TelegramServiceInterface $telegram
    )
    {
    }

    function defaultAction(TelegramEventMessageInterface $message): void
    {
        $response = $message->newResponseMessage('Вы сказали: ' . $message->getText());
        $this->telegram->sendMessage($response);
    }
}
