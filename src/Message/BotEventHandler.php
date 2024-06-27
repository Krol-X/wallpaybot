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
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class BotEventHandler extends TelegramEventHandler
{
    private const CREATE_PAYMENT = 'Создать платеж';
    private const PAYMENTS_LIST = 'Платежи';
    private const PAYMENT_CREATED = 'Платеж создан';
    private const PAYMENT_PAID = 'Платеж оплачен';
    private const PAYMENT_CANCELED = 'Платеж отменён';
    private const NO_PAYMENTS = 'У Вас нет платежей';
    private const PAYMENT_COUNT = 'У Вас %d платежей:';
    private const NEW_DISCOUNT_PAYMENT = 'Создан новый платеж со скидкой';

    public function __construct(
        private readonly TelegramServiceInterface $telegram,
        private readonly AppService               $appService,
        private readonly MessageBusInterface      $bus,
        private bool                              $useRedis
    )
    {
        parent::__construct($useRedis);
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
        $this->appService->acceptPayment($message);

        $this->telegram->SendMessage(
            $message->newResponse(self::PAYMENT_PAID)
        );
        return true;
    }

    #[OnTelegramQuery(pattern: "/^cancel-payment \d+$/")]
    public function CancelPayment(TelegramEventMessageInterface $message): bool
    {
        $this->appService->cancelPayment($message);

        $this->telegram->SendMessage(
            $message->newResponse(self::PAYMENT_CANCELED)
        );

        $newMessage = new TelegramEventMessage([
            'callback_query' => [
                'from' => $message->getFromData(),
                'data' => 'discount-payment'
            ]
        ]);

        $newMessage->setDelay(10)->send($this->bus);
        return true;
    }

    #[OnTelegramQuery(command: 'discount-payment')]
    public function DiscountPayment(TelegramEventMessageInterface $message): bool
    {
        $this->appService->createPayment($message, true);

        $this->telegram->SendMessage(
            $message->newResponse(self::NEW_DISCOUNT_PAYMENT)
        );
        return true;
    }

    #[OnTelegramMessage(command: self::PAYMENTS_LIST)]
    public function PaymentsList(TelegramEventMessageInterface $message): bool
    {
        $payments = $this->appService->getPaymentList($message);

        if (count($payments) == 0) {
            $this->telegram->SendMessage(
                $message->newResponse(self::NO_PAYMENTS)
            );
        } else {
            $keyboard = new InlineKeyboard();
            foreach ($payments as $payment) {
                $id = $payment->getId();
                $keyboard->addButton("Платеж $id", "payment-info $id");
            }

            $this->telegram->SendMessage(
                $message->newResponse(sprintf(self::PAYMENT_COUNT, count($payments)))
                    ->withReplyMarkup($keyboard)
            );
        }

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
