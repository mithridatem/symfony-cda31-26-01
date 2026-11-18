<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\CategoryService;

class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryService $categoryService
    )  {}

    #[Route(path:'/categories', name: 'app_categories')]
    public function showAllCategories(): Response
    {
        //Récupération de toutes les catégories de la BDD
        $categories = $this->categoryService->getCategories();

        return $this->render('category/show_all_categories.html.twig',[
            'categories' => $categories
        ]);
    }

    #[Route('/category/{id}', name:'app_category')]
    public function showCategoryById(int $id): Response
    {
        return $this->render('category/show_category_id.html.twig', [
            "category" => $this->categoryService->getCategoryById($id)
        ]);
    }
}
