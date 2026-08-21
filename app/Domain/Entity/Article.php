<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use DateTimeImmutable;

final readonly class Article
{
    /** @param list<Category> $categories */
    public function __construct(
        public int $id,
        public string $title,
        public string $slug,
        public string $imagePath,
        public string $description,
        public string $body,
        public int $views,
        public DateTimeImmutable $publishedAt,
        public array $categories = [],
    ) {
    }
}
