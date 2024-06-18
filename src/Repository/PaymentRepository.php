<?php

namespace App\Repository;

use App\Entity\Payment;
use App\Entity\Tariff;
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
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $em
    )
    {
        parent::__construct($registry, Payment::class);
    }

    public function createPayment(User $user, Tariff $tariff): Payment
    {
        $payment = (new Payment())
            ->setAmount(100)
            ->setStatus('created')
            ->setTariff($tariff)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setPaidAt(null);
        $user->addPayment($payment);

        $this->em->persist($payment);
        $this->em->persist($user);
        $this->em->flush();

        return $payment;
    }

    public function markAsPaid(Payment $payment): void
    {
        $payment->setStatus('paid')
            ->setPaidAt(new \DateTimeImmutable());
        $this->em->flush();
    }

    public function cancelPayment(Payment $payment): void
    {
        $payment->setStatus('canceled');
        $this->em->flush();
    }
}
