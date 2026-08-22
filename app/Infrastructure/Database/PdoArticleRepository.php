<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Application\ArticleSort;
use App\Domain\Entity\Article;
use App\Domain\Entity\Category;
use App\Domain\Repository\ArticleRepositoryInterface;
use DateTimeImmutable;
use PDO;

final readonly class PdoArticleRepository implements ArticleRepositoryInterface
{
    public function __construct(private PDO $pdo)
    {
    }

    /** @param list<int> $categoryIds @return array<int, list<Article>> */
    public function findLatestByCategoryIds(array $categoryIds, int $limit): array
    {
        $grouped = array_fill_keys($categoryIds, []);

        if ($categoryIds === [] || $limit <= 0) {
            return $grouped;
        }

        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
        $statement = $this->pdo->prepare(
            "SELECT ranked.category_id, a.id, a.title, a.slug, a.image_path, a.description, a.body, a.views, a.published_at
             FROM (
                 SELECT ac.category_id, ac.article_id,
                        ROW_NUMBER() OVER (
                            PARTITION BY ac.category_id
                            ORDER BY a.published_at DESC, a.id DESC
                        ) AS rn
                 FROM article_category ac
                 INNER JOIN articles a ON a.id = ac.article_id
                 WHERE ac.category_id IN ({$placeholders})
             ) AS ranked
             INNER JOIN articles a ON a.id = ranked.article_id
             WHERE ranked.rn <= ?
             ORDER BY ranked.category_id ASC, ranked.rn ASC",
        );
        $statement->execute([...$categoryIds, $limit]);
        $rows = $statement->fetchAll();

        $categoriesByArticle = $this->fetchCategoriesForArticles(
            array_map(static fn (array $row): int => (int) $row['id'], $rows),
        );

        foreach ($rows as $row) {
            $articleId = (int) $row['id'];
            $grouped[(int) $row['category_id']][] = $this->hydrateArticle($row, $categoriesByArticle[$articleId] ?? []);
        }

        return $grouped;
    }

    /** @return list<Article> */
    public function findPaginated(ArticleSort $sort, int $limit, int $offset): array
    {
        $statement = $this->pdo->prepare(
            "SELECT id, title, slug, image_path, description, body, views, published_at
             FROM articles
             ORDER BY {$this->orderByClause($sort)}
             LIMIT :limit OFFSET :offset",
        );
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $this->hydrateArticles($statement->fetchAll());
    }

    /** @return list<Article> */
    public function findPaginatedByCategory(int $categoryId, ArticleSort $sort, int $limit, int $offset): array
    {
        $statement = $this->pdo->prepare(
            "SELECT a.id, a.title, a.slug, a.image_path, a.description, a.body, a.views, a.published_at
             FROM articles a
             INNER JOIN article_category ac ON ac.article_id = a.id
             WHERE ac.category_id = :categoryId
             ORDER BY {$this->orderByClause($sort)}
             LIMIT :limit OFFSET :offset",
        );
        $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $this->hydrateArticles($statement->fetchAll());
    }

    public function countAll(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn();
    }

    public function countByCategory(int $categoryId): int
    {
        $statement = $this->pdo->prepare(
            'SELECT COUNT(*) FROM article_category WHERE category_id = :categoryId',
        );
        $statement->execute(['categoryId' => $categoryId]);

        return (int) $statement->fetchColumn();
    }

    public function findBySlug(string $slug): ?Article
    {
        $statement = $this->pdo->prepare(
            'SELECT id, title, slug, image_path, description, body, views, published_at
             FROM articles
             WHERE slug = :slug',
        );
        $statement->execute(['slug' => $slug]);
        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        $articleId = (int) $row['id'];
        $categoriesByArticle = $this->fetchCategoriesForArticles([$articleId]);

        return $this->hydrateArticle($row, $categoriesByArticle[$articleId] ?? []);
    }

    public function incrementViews(int $articleId): void
    {
        $statement = $this->pdo->prepare('UPDATE articles SET views = views + 1 WHERE id = :id');
        $statement->execute(['id' => $articleId]);
    }

    /** @return list<Article> */
    public function findRelated(int $articleId, int $limit): array
    {
        if ($limit <= 0) {
            return [];
        }

        $statement = $this->pdo->prepare(
            'SELECT DISTINCT a.id, a.title, a.slug, a.image_path, a.description, a.body, a.views, a.published_at
             FROM articles a
             INNER JOIN article_category ac ON ac.article_id = a.id
             WHERE a.id != :excludedId
               AND ac.category_id IN (
                   SELECT category_id FROM article_category WHERE article_id = :sourceId
               )
             ORDER BY a.published_at DESC, a.id DESC
             LIMIT :limit',
        );
        $statement->bindValue(':excludedId', $articleId, PDO::PARAM_INT);
        $statement->bindValue(':sourceId', $articleId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $this->hydrateArticles($statement->fetchAll());
    }

    private function orderByClause(ArticleSort $sort): string
    {
        return match ($sort) {
            ArticleSort::Views => 'views DESC, id DESC',
            ArticleSort::Date => 'published_at DESC, id DESC',
        };
    }

    /** @param list<array<string, mixed>> $rows @return list<Article> */
    private function hydrateArticles(array $rows): array
    {
        if ($rows === []) {
            return [];
        }

        $categoriesByArticle = $this->fetchCategoriesForArticles(
            array_map(static fn (array $row): int => (int) $row['id'], $rows),
        );

        return array_map(
            fn (array $row): Article => $this->hydrateArticle($row, $categoriesByArticle[(int) $row['id']] ?? []),
            $rows,
        );
    }

    /** @param array<string, mixed> $row @param list<Category> $categories */
    private function hydrateArticle(array $row, array $categories): Article
    {
        return new Article(
            (int) $row['id'],
            (string) $row['title'],
            (string) $row['slug'],
            (string) $row['image_path'],
            (string) $row['description'],
            (string) $row['body'],
            (int) $row['views'],
            new DateTimeImmutable((string) $row['published_at']),
            $categories,
        );
    }

    /** @param list<int> $articleIds @return array<int, list<Category>> */
    private function fetchCategoriesForArticles(array $articleIds): array
    {
        if ($articleIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($articleIds), '?'));
        $statement = $this->pdo->prepare(
            "SELECT ac.article_id, c.id, c.name, c.slug, c.description
             FROM article_category ac
             INNER JOIN categories c ON c.id = ac.category_id
             WHERE ac.article_id IN ({$placeholders})
             ORDER BY c.name ASC",
        );
        $statement->execute($articleIds);

        $grouped = [];

        foreach ($statement->fetchAll() as $row) {
            $grouped[(int) $row['article_id']][] = new Category(
                (int) $row['id'],
                (string) $row['name'],
                (string) $row['slug'],
                (string) $row['description'],
            );
        }

        return $grouped;
    }
}
