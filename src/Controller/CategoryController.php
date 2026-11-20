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
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

    #[Route(path:'/category/new', name:'app_category_new', methods:["GET", "POST"])]
    #[IsGranted('ROLE_ADMIN', message:'Vous n\'est pas autorisé')]
    public function new(Request $request):Response 
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            //ajouter avec le CategoryService::saevCategory
            $msg = $this->categoryService->saveCategory($category);

            $this->addFlash('notice', $msg);

            return $this->redirectToRoute('app_category_new');
        }

        return $this->render('category/category_new.html.twig', [
            'form'=> $form
        ]);
    }
}
