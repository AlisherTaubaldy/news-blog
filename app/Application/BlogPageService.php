<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Repository\ArticleRepositoryInterface;

final readonly class BlogPageService
{
    private const PER_PAGE = 6;

    public function __construct(private ArticleRepositoryInterface $articles)
    {
    }

    /** @return array<string, mixed> */
    public function getViewData(?string $sortParam, ?string $pageParam): array
    {
        $sort = ArticleSort::tryFrom((string) $sortParam) ?? ArticleSort::Date;
        $total = $this->articles->countAll();
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = self::sanitizePage($pageParam, $totalPages);
        $offset = ($page - 1) * self::PER_PAGE;

        $articles = $this->articles->findPaginated($sort, self::PER_PAGE, $offset);

        return [
            'articles' => array_map(ArticlePresenter::toSummary(...), $articles),
            'sorting' => self::buildSorting($sort, '/blogs'),
            'pagination' => self::buildPagination($page, $totalPages, '/blogs', $sort),
            'totalCount' => $total,
        ];
    }

    public static function sanitizePage(?string $pageParam, int $totalPages): int
    {
        if ($pageParam === null) {
            return 1;
        }

        $page = filter_var($pageParam, FILTER_VALIDATE_INT);

        if ($page === false) {
            return 1;
        }

        return max(1, min($page, $totalPages));
    }

    /** @return list<array<string, mixed>> */
    public static function buildSorting(ArticleSort $active, string $basePath): array
    {
        return [
            [
                'label' => 'Сначала новые',
                'url' => $basePath . '?sort=' . ArticleSort::Date->value,
                'active' => $active === ArticleSort::Date,
            ],
            [
                'label' => 'По просмотрам',
                'url' => $basePath . '?sort=' . ArticleSort::Views->value,
                'active' => $active === ArticleSort::Views,
            ],
        ];
    }

    /** @return list<array<string, mixed>> */
    public static function buildPagination(int $currentPage, int $totalPages, string $basePath, ArticleSort $sort): array
    {
        $pages = [];

        for ($number = 1; $number <= $totalPages; $number++) {
            $pages[] = [
                'number' => $number,
                'url' => sprintf('%s?sort=%s&page=%d', $basePath, $sort->value, $number),
                'current' => $number === $currentPage,
            ];
        }

        return $pages;
    }
}
