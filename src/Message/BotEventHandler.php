<?php

namespace App\Message;

use App\Abstract\Message\TelegramEventHandler;
use App\Attribute\OnTelegramMessage;
use App\Interface\Message\TelegramEventMessageInterface;
use App\Interface\Service\TelegramServiceInterface;
use App\Model\Keyboard\ReplyKeyboard;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class BotEventHandler extends TelegramEventHandler
{
    public function __construct(
        private readonly TelegramServiceInterface $telegram
    )
    {
    }

    #[OnTelegramMessage(command: '/start')]
    public function start(TelegramEventMessageInterface $message): bool
    {
        $keyboard = (new ReplyKeyboard())
            ->addButton('Создать платеж')
            ->addButton('Платежи');

        $this->telegram->SendMessage(
            $message->newResponse('Добрый день!')
                ->withReplyMarkup($keyboard)
        );
        return true;
    }


    function defaultAction(TelegramEventMessageInterface $message): void
    {
        $response = $message->newResponse('Вы сказали: ' . $message->getText());
        $this->telegram->sendMessage($response);
    }
}
