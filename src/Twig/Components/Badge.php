<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Badge
{
    public string $label;

    public string $variant = 'secondary';
}
