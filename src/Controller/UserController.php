<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Entity\User;
use App\DTO\UserListDTO;
use App\DTO\UserToUserDTOWrapper;

final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly NormalizerInterface $normalizer
    ) {}

    #[Route(path: '/users', name: 'app_users')]
    public function showAllUsers(): Response
    {
        $users = $this->userService->getUsers();

        return $this->render('user/show_all_users.html.twig', [
            "users" => $users
        ]);
    }

    #[Route(path: '/usersv2', name: 'app_users')]
    public function showAllUsersv2(): Response
    {
        $users = $this->userService->getUsers();
        $data = $this->normalizer->normalize($users, null, [
            "attributes" => [
                "firstname",
                "lastname",
                "email"
            ]
        ]);
        return $this->render('user/show_all_users_v2.html.twig', [
            "users" => $data
        ]);
    }

    #[Route(path: '/users/{id}', name: 'app_user')]
    public function showUserById(int $id): Response
    {
        $user = $this->userService->getUserById($id);

        return $this->render('user/show_user_id.html.twig', [
            "user" => $user
        ]);
    }

    #[Route(path: '/users/dto/{id}', name: 'app_user_dto')]
    public function showUserListDTOById(int $id): Response
    {
        $user = $this->userService->getUserById($id);

        if ($user) UserToUserDTOWrapper::wrapUserToUserListDTO($user);
        
        return $this->render('user/show_user_dto_id.html.twig', [
            "user" => $user ?? null
        ]);
    }

    
    #[Route(path: '/users/dtov2/{id}', name: 'app_user_dto')]
    public function showUserListDTOByIdV2(int $id): Response
    {
        $user = $this->userService->getUserListDTOById($id);
        
        return $this->render('user/show_user_dto_id.html.twig', [
            "user" => $user ?? null
        ]);
    }
}
