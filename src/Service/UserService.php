<?php

namespace App\Service;

use App\DTO\UserListDTO;
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

    public function getUserListDTOById(int $id): ?UserListDTO
    {
        return $this->userRepository->findOneUserListDTOById($id);
    }

    public function getAllUserListDTO(): array 
    {
        return $this->userRepository->findAllUserListDTO();
    }
}
