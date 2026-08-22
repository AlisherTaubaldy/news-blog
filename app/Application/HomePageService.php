<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Entity\Category;
use App\Domain\Repository\ArticleRepositoryInterface;
use App\Domain\Repository\CategoryRepositoryInterface;

final readonly class HomePageService
{
    private const ARTICLES_PER_CATEGORY = 3;

    public function __construct(
        private CategoryRepositoryInterface $categories,
        private ArticleRepositoryInterface $articles,
    ) {
    }

    /** @return array<string, mixed> */
    public function getViewData(): array
    {
        $categories = $this->categories->findWithPublishedArticles();
        $categoryIds = array_map(static fn (Category $category): int => $category->id, $categories);
        $articlesByCategory = $this->articles->findLatestByCategoryIds($categoryIds, self::ARTICLES_PER_CATEGORY);

        $sections = array_map(
            static fn (Category $category): array => CategoryPresenter::toLink($category) + [
                'articles' => array_map(ArticlePresenter::toSummary(...), $articlesByCategory[$category->id] ?? []),
            ],
            $categories,
        );

        return ['categories' => $sections];
    }
}
