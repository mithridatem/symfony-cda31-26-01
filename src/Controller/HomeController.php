<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'app_home')]
    public function index(): Response
    {
        $formations = [
            [
                'title' => 'Développement PHP',
                'description' => 'Découvrir le développement côté serveur.',
            ],
            [
                'title' => 'Symfony',
                'description' => 'Structurer une application web en PHP.',
            ],
            [
                'title' => 'Java-script',
                'description' => 'Manipuler la partie Front-end d\'une application web.',
            ],
            [
                'title' => 'Cypress',
                'description' => 'Réaliser des tests end2end sur la partie Front-end d\'une application web.',
            ],
        ];
        return $this->render('home/index.html.twig', [
            "messages" => [
                "Bienvenue sur le site !",
                "Enregistrement réussi.",
                "Une erreur est survenue."
            ],
            "types" => [
                "",
                "success",
                "danger"
            ],
            "formations" => $formations
        ]);
    }
}
