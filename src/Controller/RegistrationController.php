<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\UploadFileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly EntityManagerInterface $em,
        private readonly UploadFileService $upload
    ) {}

    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setIcon('profil.png');

            //Récupération du fichier
            $icon = $form->get('icon')->getData();
            //test si le fichier est importé
            if ($icon) {
                try {
                    //Appel du service
                    $newFilename = $this->upload->upload($icon);

                    //Set du nom du fichier renommé
                    $user->setIcon($newFilename);
                } catch (FileException $e) {
                    
                    $this->addFlash('warning', $e->getMessage());

                    return $this->redirectToRoute('app_register');
                }
            }

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Le compte a été ajouté avec succés');

            return $this->redirectToRoute('app_register');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
