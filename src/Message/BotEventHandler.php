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
    private const RESPONSE_PAID = 'Платеж оплачен';
    private const RESPONSE_CANCELED = 'Платеж отменён';
    private const PAYMENT_COUNT = 'У Вас %d платежей:';

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
        $payment_id = $payment->getId();

        $keyboard = (new InlineKeyboard())
            ->addButton("Платеж {$payment->getAmount()} руб.", "apply-payment {$payment_id}")
            ->addButton("Отменить", "cancel-payment {$payment_id}");

        $this->telegram->SendMessage(
            $message->newResponse(self::PAYMENT_CREATED)
                ->withReplyMarkup($keyboard)
        );
        return true;
    }

    #[OnTelegramQuery(pattern: "/^apply-payment \d+$/")]
    public function ApplyPayment(TelegramEventMessageInterface $message): bool
    {
        $this->appService->applyPayment($message);

        $this->telegram->SendMessage(
            $message->newResponse(self::RESPONSE_PAID)
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
        $payment = $paymentEntity->toFormattedArray();

        $responseMessage = "Платёж {$payment['id']}";
        if ($payment['is_discount']) {
            $responseMessage .= ' (со скидкой)';
        }
        $responseMessage .= "\nСтатус: {$payment['status']}\n";
        $responseMessage .= "Цена: {$payment['amount']} руб.\n";
        $responseMessage .= "Тариф: {$payment['tariff']}\n";
        $responseMessage .= "Дата создания: {$payment['created_at']}\n";

        if ($payment['paid_at']) {
            $responseMessage .= "Дата оплаты: {$payment['paid_at']}\n";
        }

        $this->telegram->SendMessage(
            $message->newResponse($responseMessage)
        );
        return true;
    }
}
