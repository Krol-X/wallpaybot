<?php

namespace App\Service;

use App\Abstract\Service\TelegramBotService;
use App\Attribute\OnTelegramMessage;

class AppBotService extends TelegramBotService implements AppBotServiceInterface
{
    #[OnTelegramMessage(command: '/start')]
    public function start($data, $chatId, $event): bool
    {
        $this->SendMessage($chatId, 'Добрый день!');
    }

    function defaultAction($data, $chatId, $event): void
    {
        $this->SendMessage($chatId, 'Вы сказали: ' . $data['text']);
    }
}
