<?php

namespace App\Tests\Factory;

use App\Factory\CategoryFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class CategoryFactoryTest extends KernelTestCase
{
    public function testFactoryUsesCustomCategoryProvider(): void
    {
        self::bootKernel();

        $categories = CategoryFactory::new()->withoutPersisting()->many(10)->create();

        self::assertCount(10, $categories);
        foreach ($categories as $category) {
            self::assertContains($category->getName(), [
                'Polar', 'Science Fiction', 'Fantastique', 'Roman',
                'Roman historique', 'Biographique', 'Thriller', 'Essai',
                'Science', 'Contes', 'Classique',
            ]);
            self::assertNull($category->getId());
        }
    }
}
