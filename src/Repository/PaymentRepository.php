<?php

namespace App\Repository;

use App\Entity\Payment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry
    )
    {
        parent::__construct($registry, Payment::class);
    }

    public function createPayment(User $user): Payment
    {
        $payment = (new Payment())
            ->setAmount(100)
            ->setStatus('created');
        $user->addPayment($payment);

        $this->em->persist($payment);
        $this->em->persist($user);
        $this->em->flush();

        return $payment;
    }

    /**
     * Установка успешного статуса платежа.
     */
    public function markAsPaid(Payment $payment): void
    {
        $payment->setStatus('paid')
            ->setPaidAt(new \DateTimeImmutable());
        $this->em->flush();
    }

    /**
     * Отмена платежа.
     */
    public function cancelPayment(Payment $payment): void
    {
        $payment->setStatus('canceled');
        $this->em->flush();
    }
}
