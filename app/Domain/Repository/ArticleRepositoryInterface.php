<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Application\ArticleSort;
use App\Domain\Entity\Article;

interface ArticleRepositoryInterface
{
    /** @param list<int> $categoryIds @return array<int, list<Article>> */
    public function findLatestByCategoryIds(array $categoryIds, int $limit): array;

    /** @return list<Article> */
    public function findPaginated(ArticleSort $sort, int $limit, int $offset): array;

    /** @return list<Article> */
    public function findPaginatedByCategory(int $categoryId, ArticleSort $sort, int $limit, int $offset): array;

    public function countAll(): int;

    public function countByCategory(int $categoryId): int;

    public function findBySlug(string $slug): ?Article;

    public function incrementViews(int $articleId): void;

    /** @return list<Article> */
    public function findRelated(int $articleId, int $limit): array;
}
