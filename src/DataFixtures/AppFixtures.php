<?php

namespace App\DataFixtures;
use App\Entity\Category;
use App\Factory\CategoryFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = CategoryFactory::createMany(10);
    }
}
