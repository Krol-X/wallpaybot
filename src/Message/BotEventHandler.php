<?php

namespace App\Message;

use App\Abstract\Message\TelegramEventHandler;
use App\Attribute\OnTelegramMessage;
use App\Attribute\OnTelegramQuery;
use App\Interface\Message\TelegramEventMessageInterface;
use App\Interface\Service\TelegramServiceInterface;
use App\Model\Keyboard\InlineKeyboard;
use App\Model\Keyboard\ReplyKeyboard;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class BotEventHandler extends TelegramEventHandler
{
    private const CREATE_PAYMENT = 'Создать платеж';
    private const PAYMENTS = 'Платежи';
    private const PAYMENT_CREATED = 'Платеж создан';
    private const RESPONSE_CANCELED = 'Платеж отменён';

    public function __construct(
        private readonly TelegramServiceInterface $telegram
    )
    {
    }

    #[OnTelegramMessage(command: '/start')]
    public function start(TelegramEventMessageInterface $message): bool
    {
        $keyboard = (new ReplyKeyboard())
            ->addButton(self::CREATE_PAYMENT)
            ->addButton(self::PAYMENTS);

        $this->telegram->SendMessage(
            $message->newResponse('Добрый день!')
                ->withReplyMarkup($keyboard)
        );
        return true;
    }

    #[OnTelegramMessage(command: self::CREATE_PAYMENT)]
    public function CreatePayment(TelegramEventMessageInterface $message): bool
    {
        $keyboard = (new InlineKeyboard())
            ->addButton("Платеж {100} руб.", '1')
            ->addButton("Отменить", 'cancel-payment-1');

        $this->telegram->SendMessage(
            $message->newResponse(self::PAYMENT_CREATED)
                ->withReplyMarkup($keyboard)
        );
        return true;
    }

    #[OnTelegramQuery(pattern: "/^cancel-payment-\d+$/")]
    public function CancelPayment(TelegramEventMessageInterface $message): bool
    {
        // $this->botService->cancelPayment($data);
        $this->telegram->SendMessage(
            $message->newResponse(self::RESPONSE_CANCELED)
        );
        return true;
    }
}
