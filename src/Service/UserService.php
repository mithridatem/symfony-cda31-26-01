<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Entity\User;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function getUsers(): array
    {
        return $this->userRepository->findAll() ?? [];
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }
}
