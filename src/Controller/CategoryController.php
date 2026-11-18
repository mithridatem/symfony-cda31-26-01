<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\CategoryService;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly NormalizerInterface $normalizer
    ) {}

    #[Route(path: '/categories', name: 'app_categories')]
    public function showAllCategories(): Response
    {
        //Récupération de toutes les catégories de la BDD
        $categories = $this->categoryService->getCategories();
        
        return $this->render('category/show_all_categories.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route(path: '/categoriesv2', name: 'app_categories')]
    public function showAllCategoriesv2(): Response
    {
        //Récupération de toutes les catégories de la BDD
        $categories = $this->categoryService->getCategories();
        $data = $this->normalizer->normalize($categories, null, ["attributes"=> ["id", "name"]]);
        return $this->render('category/show_all_categories_v2.html.twig', [
            'categories' => $data
        ]);
    }

    #[Route('/category/{id}', name: 'app_category')]
    public function showCategoryById(int $id): Response
    {
        return $this->render('category/show_category_id.html.twig', [
            "category" => $this->categoryService->getCategoryById($id)
        ]);
    }
}
