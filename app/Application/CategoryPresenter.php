<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Entity\Category;

final class CategoryPresenter
{
    /** @return array<string, mixed> */
    public static function toLink(Category $category): array
    {
        return [
            'name' => $category->name,
            'description' => $category->description,
            'url' => '/categories/' . $category->slug,
            'articleCount' => $category->articleCount,
        ];
    }
}
