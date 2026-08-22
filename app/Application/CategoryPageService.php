<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Repository\ArticleRepositoryInterface;
use App\Domain\Repository\CategoryRepositoryInterface;

final readonly class CategoryPageService
{
    private const PER_PAGE = 6;

    public function __construct(
        private CategoryRepositoryInterface $categories,
        private ArticleRepositoryInterface $articles,
    ) {
    }

    /** @return array<string, mixed> */
    public function getIndexViewData(): array
    {
        return [
            'categories' => array_map(CategoryPresenter::toLink(...), $this->categories->findAllWithArticleCount()),
        ];
    }

    /** @return array<string, mixed>|null */
    public function getShowViewData(string $slug, ?string $sortParam, ?string $pageParam): ?array
    {
        $category = $this->categories->findBySlug($slug);

        if ($category === null) {
            return null;
        }

        $sort = ArticleSort::tryFrom((string) $sortParam) ?? ArticleSort::Date;
        $total = $this->articles->countByCategory($category->id);
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = BlogPageService::sanitizePage($pageParam, $totalPages);
        $offset = ($page - 1) * self::PER_PAGE;

        $articles = $this->articles->findPaginatedByCategory($category->id, $sort, self::PER_PAGE, $offset);
        $basePath = '/categories/' . $category->slug;

        return [
            'category' => [
                'name' => $category->name,
                'description' => $category->description,
            ],
            'articles' => array_map(ArticlePresenter::toSummary(...), $articles),
            'sorting' => BlogPageService::buildSorting($sort, $basePath),
            'pagination' => BlogPageService::buildPagination($page, $totalPages, $basePath, $sort),
            'totalCount' => $total,
        ];
    }
}
