<?php

namespace App\Repository;

use App\Entity\PaymentStatistic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaymentStatistic>
 */
class PaymentStatisticRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $em
    )
    {
        parent::__construct($registry, PaymentStatistic::class);
    }

    public function updateStatistics(
        int   $totalPayments,
        float $successfulPaymentRatio,
        float $discountedSuccessfulPaymentRatio,
        float $failedPaymentRatio,
        float $totalRevenue
    ): void
    {
        $statistic = (new PaymentStatistic())
            ->setTotalPayments($totalPayments)
            ->setSuccessfulPaymentRatio($successfulPaymentRatio)
            ->setDiscountedSuccessfulPaymentRatio($discountedSuccessfulPaymentRatio)
            ->setFailedPaymentRatio($failedPaymentRatio)
            ->setTotalRevenue($totalRevenue)
            ->setCreatedAt();

        $this->em->persist($statistic);
        $this->em->flush();
    }
}
