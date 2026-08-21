<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Category;

interface CategoryRepositoryInterface
{
    /** @return list<Category> */
    public function findAllWithArticleCount(): array;

    /** @return list<Category> */
    public function findWithPublishedArticles(): array;

    public function findBySlug(string $slug): ?Category;
}
