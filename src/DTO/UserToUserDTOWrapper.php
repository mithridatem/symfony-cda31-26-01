<?php

namespace App\DTO;

use App\Entity\User;
use App\DTO\UserListDTO;

final class UserToUserDTOWrapper
{
    public static function wrapUserToUserListDTO(User $user): UserListDTO
    {
        return new UserListDTO(
            $user->getFirstname(),
            $user->getLastname(),
            $user->getEmail()
        );
    }
}
