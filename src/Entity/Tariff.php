<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TariffRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TariffRepository::class)]
#[ApiResource]
class Tariff
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?int $discount_percentage = null;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'user')]
    private Collection $payments;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(bool $withDiscount = false): ?float
    {
        return $withDiscount ? $this->price * ($this->discount_percentage * 0.01) : $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDiscountPercentage(): ?int
    {
        return $this->discount_percentage;
    }

    public function setDiscountPercentage(?int $discount_percentage): void
    {
        $this->discount_percentage = $discount_percentage;
    }

    public function getPayments(): Collection
    {
        return $this->payments;
    }
}
