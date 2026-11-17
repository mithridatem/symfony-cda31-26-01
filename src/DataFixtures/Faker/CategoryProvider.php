<?php

namespace App\DataFixtures\Faker;

use Faker\Provider\Base;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('foundry.faker_provider')]
final class CategoryProvider
{
    private const GENRES = [
        "Polar",
        "Science Fiction",
        "Fantastique",
        "Roman",
        "Roman historique",
        "Biographique",
        "Thriller",
        "Essai",
        "Science",
        "Contes",
        "Classique"
    ];

    public function getCategoryName(): string
    {
        return Base::randomElement(self::GENRES);
    }
}
