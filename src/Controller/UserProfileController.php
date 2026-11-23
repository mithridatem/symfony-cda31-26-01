<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserProfileController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    #[Route(path:'/profile/edit', name:'user_profile_edit', methods:['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(#[CurrentUser] User $user, Request $request): Response
    {
        $form = $this->createForm(ProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $this->userService->editProfile($user);

            $this->addFlash('notice', 'Utilisateur mise à jour avec succés');

            return $this->redirectToRoute('user_profile_edit');
        }

        return $this->render('user/profile_edit.html.twig', [
            'form'=> $form
        ]);
    }
}