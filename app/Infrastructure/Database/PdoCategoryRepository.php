<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Entity\Category;
use App\Domain\Repository\CategoryRepositoryInterface;
use PDO;

final readonly class PdoCategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findAllWithArticleCount(): array
    {
        return $this->fetchCategories(
            'SELECT c.id, c.name, c.slug, c.description, COUNT(ac.article_id) AS article_count
             FROM categories c
             LEFT JOIN article_category ac ON ac.category_id = c.id
             GROUP BY c.id, c.name, c.slug, c.description
             ORDER BY c.name ASC',
        );
    }

    public function findWithPublishedArticles(): array
    {
        return $this->fetchCategories(
            'SELECT c.id, c.name, c.slug, c.description, COUNT(ac.article_id) AS article_count
             FROM categories c
             INNER JOIN article_category ac ON ac.category_id = c.id
             INNER JOIN articles a ON a.id = ac.article_id
             GROUP BY c.id, c.name, c.slug, c.description
             HAVING COUNT(a.id) > 0
             ORDER BY c.name ASC',
        );
    }

    public function findBySlug(string $slug): ?Category
    {
        $statement = $this->pdo->prepare(
            'SELECT c.id, c.name, c.slug, c.description, COUNT(ac.article_id) AS article_count
             FROM categories c
             LEFT JOIN article_category ac ON ac.category_id = c.id
             WHERE c.slug = :slug
             GROUP BY c.id, c.name, c.slug, c.description',
        );
        $statement->execute(['slug' => $slug]);
        $row = $statement->fetch();

        return $row === false ? null : $this->hydrate($row);
    }

    /** @return list<Category> */
    private function fetchCategories(string $sql): array
    {
        $rows = $this->pdo->query($sql)->fetchAll();

        return array_map($this->hydrate(...), $rows);
    }

    /** @param array<string, mixed> $row */
    private function hydrate(array $row): Category
    {
        return new Category(
            (int) $row['id'],
            (string) $row['name'],
            (string) $row['slug'],
            (string) $row['description'],
            (int) $row['article_count'],
        );
    }
}
