<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\CategoryService;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Form\CategoryType;
use App\Entity\Category;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly NormalizerInterface $normalizer
    ) {}

    #[Route(path: '/categories', name: 'app_categories')]
    #[IsGranted('ROLE_USER')]
    public function showAllCategories(): Response
    {
        //Récupération de toutes les catégories de la BDD
        $categories = $this->categoryService->getCategories();

        return $this->render('category/show_all_categories.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route(path: '/categoriesv2', name: 'app_categories')]
    #[IsGranted('ROLE_USER')]
    public function showAllCategoriesv2(): Response
    {
        //Récupération de toutes les catégories de la BDD
        $categories = $this->categoryService->getCategories();
        $data = $this->normalizer->normalize($categories, null, ["attributes" => ["id", "name"]]);
        return $this->render('category/show_all_categories_v2.html.twig', [
            'categories' => $data
        ]);
    }

    #[Route(path: '/category/new', name: 'app_category_new', methods: ["GET", "POST"])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'est pas autorisé')]
    public function new(Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            //ajouter avec le CategoryService::saveCategory
            $msg = $this->categoryService->saveOrUpdateCategory($category);

            $this->addFlash('notice', $msg);

            return $this->redirectToRoute('app_category_new');
        }

        return $this->render('category/category_new_edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route(path: '/category/{id}/edit', name: 'app_category_edit')]
    public function edit(int $id, Request $request): Response
    {
        $category = $this->categoryService->getCategoryById($id);
        //Test si la catégorie n'existe pas
        if (null == $category) {

            $this->addFlash('notice', "Categorie n'existe pas");

            return $this->redirectToRoute('app_category_new');
        } 
            
        $form = $this->createForm(CategoryType::class, $category, [
            'label_save_edit' => 'Modifier'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->categoryService->saveOrUpdateCategory($category);

            $this->addFlash('notice', 'Categorie mise à jour');

            return $this->redirectToRoute('app_category_edit' , ['id'=> $id]);
        }

        return $this->render(
            'category/category_new_edit.html.twig', [
                'form' => $form
            ]
        );
    }
}
