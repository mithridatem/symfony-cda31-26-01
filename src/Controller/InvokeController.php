<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path:'/invoke', name:'app_invoke')]
class InvokeController
{
    #[Template('invoke/invoke.html.twig')]
    public function __invoke(): array
    {
        return [
            "info"=> "Bienvenue sur le controller invoke"
        ];
    }
}
