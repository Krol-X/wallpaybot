<?php

namespace App\Service;

use App\Entity\Payment;
use App\Entity\User;
use App\Interface\Message\TelegramEventMessageInterface;
use App\Repository\PaymentRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;

class AppService
{
    public function __construct(
        private readonly UserRepository    $userRepository,
        private readonly PaymentRepository $paymentRepository
    )
    {
    }

    public function findOrCreateUser(TelegramEventMessageInterface $message): User
    {
        $user = $this->userRepository->find($message->getFromId());

        if (!$user) {
            $data = $message->getFromData();
            $user = $this->userRepository->createUser($data);
        }

        return $user;
    }

    public function createPayment(TelegramEventMessageInterface $message): Payment
    {
        $user = $this->userRepository->find($message->getFromId());
        if (!$user) {
            // todo: exception
        }

        $payment = $this->paymentRepository->createPayment($user);
        return $payment;
    }

    public function acceptPayment(Payment $payment): void
    {
        $this->paymentRepository->markAsPaid($payment);
    }

    public function cancelPayment(TelegramEventMessageInterface $message): void
    {
        $command = $message->getText();
        $payment_id = explode(' ', $command[1]);

        $payment = $this->paymentRepository->find($payment_id);
        $this->paymentRepository->cancelPayment($payment);
    }

    /**
     * @var Collection<int, Payment>
     */
    public function getPaymentList(TelegramEventMessageInterface $message): Collection
    {
        $user = $this->userRepository->find($message->getFromId());
        if (!$user) {
            // todo: exception
        }

        $payments = $user->getPayments();
        return $payments;
    }

    public function getPayment(TelegramEventMessageInterface $message): Payment
    {
        $command = $message->getText();
        $payment_id = explode(' ', $command)[1];

        $payment = $this->paymentRepository->find($payment_id);
        return $payment;
    }
}
