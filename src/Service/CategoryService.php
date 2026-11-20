<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private EntityManagerInterface $em
    ) {}

    public function getCategories(): array
    {
        return $this->categoryRepository->findAll() ?? [];
    }

    public function getCategoryById(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }


    public function saveOrUpdateCategory(Category $category): string 
    {
        $this->em->persist($category);
        $this->em->flush();

        return "La categorie a été ajouté ";
    }
}
