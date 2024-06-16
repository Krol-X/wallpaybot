<?php

namespace App\Service;

use App\Repository\UserRepository;

class AppService
{
    public function __construct(
        private readonly UserRepository $userRepository
    )
    {
    }

    public function updateUser(int $telegram_id, array $data)
    {
        $user = $this->userRepository->findOrCreate($telegram_id, $data);
        // todo: add avatar and update user
    }
}