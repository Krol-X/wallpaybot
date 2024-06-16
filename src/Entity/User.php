<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
// use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource]
class User
{
    // use TimestampableEntity;

    // string for 32-bit if it needs
    #[ORM\Id]
    #[ORM\Column(type: Types::BIGINT)]
    private ?int $telegram_id = null;

    #[ORM\Column(length: 64)]
    private ?string $first_name = null;

    #[ORM\Column(length: 64)]
    private ?string $last_name = null;

    #[ORM\Column(length: 32)]
    private ?string $username = null;

    #[ORM\Column(length: 4)]
    private ?string $language_code = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private ?string $profile_image = null;

    public function getTelegramId(): ?int
    {
        return $this->telegram_id;
    }

    public function setTelegramId(int $telegram_id): static
    {
        $this->telegram_id = $telegram_id;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getLanguageCode(): ?string
    {
        return $this->language_code;
    }

    public function setLanguageCode(string $language_code): static
    {
        $this->language_code = $language_code;

        return $this;
    }

    public function getProfileImage(): ?string
    {
        return $this->profile_image;
    }

    public function setProfileImage(?string $profile_image): static
    {
        $this->profile_image = $profile_image;

        return $this;
    }
}
