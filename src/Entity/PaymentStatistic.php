<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PaymentStatisticRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentStatisticRepository::class)]
#[ApiResource]
class PaymentStatistic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $totalPayments = null;

    #[ORM\Column]
    private ?float $successfulPaymentRatio = null;

    #[ORM\Column]
    private ?float $discountedSuccessfulPaymentRatio = null;

    #[ORM\Column]
    private ?float $failedPaymentRatio = null;

    #[ORM\Column]
    private ?float $totalRevenue = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalPayments(): ?int
    {
        return $this->totalPayments;
    }

    public function setTotalPayments(int $totalPayments): static
    {
        $this->totalPayments = $totalPayments;

        return $this;
    }

    public function getSuccessfulPaymentRatio(): ?float
    {
        return $this->successfulPaymentRatio;
    }

    public function setSuccessfulPaymentRatio(float $successfulPaymentRatio): static
    {
        $this->successfulPaymentRatio = $successfulPaymentRatio;

        return $this;
    }

    public function getDiscountedSuccessfulPaymentRatio(): ?float
    {
        return $this->discountedSuccessfulPaymentRatio;
    }

    public function setDiscountedSuccessfulPaymentRatio(float $discountedSuccessfulPaymentRatio): static
    {
        $this->discountedSuccessfulPaymentRatio = $discountedSuccessfulPaymentRatio;

        return $this;
    }

    public function getFailedPaymentRatio(): ?float
    {
        return $this->failedPaymentRatio;
    }

    public function setFailedPaymentRatio(float $failedPaymentRatio): static
    {
        $this->failedPaymentRatio = $failedPaymentRatio;

        return $this;
    }

    public function getTotalRevenue(): ?float
    {
        return $this->totalRevenue;
    }

    public function setTotalRevenue(float $totalRevenue): static
    {
        $this->totalRevenue = $totalRevenue;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $calculatedAt = new \DateTimeImmutable()): static
    {
        $this->createdAt = $calculatedAt;

        return $this;
    }
}
