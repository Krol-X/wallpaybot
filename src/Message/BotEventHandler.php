<?php

namespace App\Message;

use App\Abstract\Message\TelegramEventHandler;
use App\Attribute\OnTelegramMessage;
use App\Attribute\OnTelegramQuery;
use App\Interface\Message\TelegramEventMessageInterface;
use App\Interface\Service\TelegramServiceInterface;
use App\Model\Keyboard\InlineKeyboard;
use App\Model\Keyboard\ReplyKeyboard;
use App\Service\AppService;
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
        private readonly TelegramServiceInterface $telegram,
        private readonly AppService               $appService
    )
    {
    }

    #[OnTelegramMessage(command: '/start')]
    public function start(TelegramEventMessageInterface $message): bool
    {
        $this->appService->findOrCreateUser($message);

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
        $payment = $this->appService->createPayment($message);

        $keyboard = (new InlineKeyboard())
            ->addButton("Платеж {$payment->getAmount()} руб.", '1')
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
        $this->appService->cancelPayment($message);

        $this->telegram->SendMessage(
            $message->newResponse(self::RESPONSE_CANCELED)
        );
        return true;
    }

    #[OnTelegramMessage(command: self::PAYMENTS_LIST)]
    public function PaymentsList(TelegramEventMessageInterface $message): bool
    {
        $payments = $this->appService->getPaymentList($message);

        $keyboard = new InlineKeyboard();
        foreach ($payments as $payment) {
            $id = $payment->getId();
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
        $paymentEntity = $this->appService->getPayment($message);
        $payment = $paymentEntity->toArray();

        $responseMessage = "Платёж {$payment['id']}\n" .
            "Статус: {$payment['status']}\n";
            "Цена: {$payment['amount']} руб.\n" .
//            "С учётом скидки: " . ($payment['is_discount'] ? 'Да' : 'Нет') . "\n" .
            "Дата создания: " . $payment['created_at']->format('Y-m-d H:i:s') . "\n";

        if ($payment['paid_at']) {
            $responseMessage .= "Дата оплаты: " . $payment['paid_at']->format('Y-m-d H:i:s') . "\n";
        }

        $this->telegram->SendMessage(
            $message->newResponse($responseMessage)
        );
        return true;
    }
}
