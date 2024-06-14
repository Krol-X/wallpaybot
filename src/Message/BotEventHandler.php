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
    private const PAYMENTS_LIST = 'Платежи';
    private const PAYMENT_CREATED = 'Платеж создан';
    private const RESPONSE_CANCELED = 'Платеж отменён';
    const PAYMENT_COUNT = 'У Вас %d платежей:';

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
            ->addButton(self::PAYMENTS_LIST);

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
            ->addButton("Отменить", "cancel-payment 1");

        $this->telegram->SendMessage(
            $message->newResponse(self::PAYMENT_CREATED)
                ->withReplyMarkup($keyboard)
        );
        return true;
    }

    #[OnTelegramQuery(pattern: "/^cancel-payment \d+$/")]
    public function CancelPayment(TelegramEventMessageInterface $message): bool
    {
        // $this->botService->cancelPayment($data);
        $this->telegram->SendMessage(
            $message->newResponse(self::RESPONSE_CANCELED)
        );
        return true;
    }

    #[OnTelegramMessage(command: self::PAYMENTS_LIST)]
    public function PaymentsList(TelegramEventMessageInterface $message): bool
    {
        $payments = [1, 2, 3, 4];

        $keyboard = new InlineKeyboard();
        foreach ($payments as $payment) {
            $id = $payment;
            $keyboard->addButton("Платеж $id", "payment-info $id");
        }

        $this->telegram->SendMessage(
            $message->newResponse(sprintf(self::PAYMENT_COUNT, count($payments)))
                ->withReplyMarkup($keyboard)
        );
        return true;
    }

    #[OnTelegramQuery(pattern: "/^payment-info \d+$/")]
    public function PaymentInfo(TelegramEventMessageInterface $message): bool
    {
        $command = $message->getText();
        $payment_id = explode(' ', $command)[1];
        $payment = [
            "id" => $payment_id,
            "status" => "test"
        ];

        $response_message = "Платёж {$payment_id}\n" .
            "Статус: test\n";
//            "Цена: {$payment->getPrice()} руб.\n" .
//            "С учётом скидки: " . ($payment->isDiscount() ? 'Да' : 'Нет') . "\n" .
//            "Дата создания: " . $payment->getCreatedAt()->format('Y-m-d H:i:s') . "\n";

//        $paidAt = $payment->getPaidAt();
//        if ($paidAt) {
//            $message .= "Дата оплаты: " . $paidAt->format('Y-m-d H:i:s') . "\n";
//        }

        $this->telegram->SendMessage(
            $message->newResponse($response_message)
        );
        return true;
    }
}
