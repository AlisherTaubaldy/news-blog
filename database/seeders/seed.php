<?php

declare(strict_types=1);

use App\Infrastructure\Database\ConnectionFactory;
use Faker\Factory;
require dirname(__DIR__, 2) . '/vendor/autoload.php';

$categories = [
    [
        'slug' => 'development',
        'name' => 'Разработка',
        'description' => 'PHP, архитектура приложений и инженерные практики.',
    ],
    [
        'slug' => 'databases',
        'name' => 'Базы данных',
        'description' => 'MySQL, моделирование данных и производительность запросов.',
    ],
    [
        'slug' => 'interfaces',
        'name' => 'Интерфейсы',
        'description' => 'Верстка, шаблоны Smarty и удобные пользовательские сценарии.',
    ],
    [
        'slug' => 'security',
        'name' => 'Безопасность',
        'description' => 'Практики защиты PHP-приложений и работы с пользовательскими данными.',
    ],
];

$images = [
    '/assets/images/preview/architecture.svg',
    '/assets/images/preview/database.svg',
    '/assets/images/preview/templates.svg',
];

try {
    $pdo = ConnectionFactory::createFromEnvironment();
    $faker = Factory::create('ru_RU');
    $faker->seed(20260820);
    $pdo->beginTransaction();

    $upsertCategory = $pdo->prepare(
        'INSERT INTO categories (name, slug, description)
         VALUES (:name, :slug, :description)
         ON DUPLICATE KEY UPDATE
             name = VALUES(name),
             description = VALUES(description)',
    );
    $findCategoryId = $pdo->prepare('SELECT id FROM categories WHERE slug = :slug');

    /** @var array<string, int> $categoryIds */
    $categoryIds = [];

    foreach ($categories as $category) {
        $upsertCategory->execute($category);
        $findCategoryId->execute(['slug' => $category['slug']]);
        $categoryId = $findCategoryId->fetchColumn();

        if ($categoryId === false) {
            throw new RuntimeException("Could not resolve category {$category['slug']}.");
        }

        $categoryIds[$category['slug']] = (int) $categoryId;
    }

    $upsertArticle = $pdo->prepare(
        'INSERT INTO articles (title, slug, image_path, description, body, views, published_at)
         VALUES (:title, :slug, :image_path, :description, :body, :views, :published_at)
         ON DUPLICATE KEY UPDATE
             title = VALUES(title),
             image_path = VALUES(image_path),
             description = VALUES(description),
             body = VALUES(body),
             views = VALUES(views),
             published_at = VALUES(published_at)',
    );
    $findArticleId = $pdo->prepare('SELECT id FROM articles WHERE slug = :slug');
    $deleteArticleCategories = $pdo->prepare('DELETE FROM article_category WHERE article_id = :article_id');
    $attachCategory = $pdo->prepare(
        'INSERT INTO article_category (article_id, category_id)
         VALUES (:article_id, :category_id)',
    );

    foreach (range(1, 36) as $number) {
        $primaryCategory = $categories[($number - 1) % count($categories)]['slug'];
        $slug = sprintf('seed-article-%02d', $number);
        $title = sprintf('Практика PHP: %s', $faker->sentence($faker->numberBetween(4, 7)));
        $description = $faker->realTextBetween(120, 180);
        $body = implode("\n\n", $faker->paragraphs(3));

        $upsertArticle->execute([
            'title' => $title,
            'slug' => $slug,
            'image_path' => $images[($number - 1) % count($images)],
            'description' => $description,
            'body' => $body,
            'views' => $faker->numberBetween(80, 5000),
            'published_at' => (new DateTimeImmutable('2026-08-20 12:00:00'))
                ->modify(sprintf('-%d days', $number - 1))
                ->format('Y-m-d H:i:s'),
        ]);

        $findArticleId->execute(['slug' => $slug]);
        $articleId = $findArticleId->fetchColumn();

        if ($articleId === false) {
            throw new RuntimeException("Could not resolve article {$slug}.");
        }

        $deleteArticleCategories->execute(['article_id' => $articleId]);
        $attachedCategories = [$primaryCategory];

        // Every fourth article belongs to two categories to verify M:N behaviour.
        if ($number % 4 === 0) {
            $attachedCategories[] = $primaryCategory === 'security'
                ? 'development'
                : 'security';
        }

        foreach (array_unique($attachedCategories) as $categorySlug) {
            $attachCategory->execute([
                'article_id' => $articleId,
                'category_id' => $categoryIds[$categorySlug],
            ]);
        }
    }

    $pdo->commit();
    fwrite(STDOUT, "Seeded 4 categories and 36 articles.\n");
} catch (Throwable $exception) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    fwrite(STDERR, "Seeding failed. Check the database configuration and schema.\n");
    throw $exception;
}
