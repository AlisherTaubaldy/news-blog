<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Entity\Article;
use DateTimeImmutable;

final class ArticlePresenter
{
    private const MONTHS = [
        1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
        5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
        9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря',
    ];

    /** @return array<string, mixed> */
    public static function toSummary(Article $article): array
    {
        return [
            'title' => $article->title,
            'description' => $article->description,
            'url' => '/blogs/' . $article->slug,
            'image_url' => $article->imagePath,
            'image_alt' => sprintf('Иллюстрация к статье «%s»', $article->title),
            'published_at_iso' => $article->publishedAt->format('Y-m-d'),
            'published_at_formatted' => self::formatDate($article->publishedAt),
            'views' => $article->views,
        ];
    }

    /** @return array<string, mixed> */
    public static function toDetail(Article $article): array
    {
        return self::toSummary($article) + [
            'categories' => array_map(CategoryPresenter::toLink(...), $article->categories),
            'paragraphs' => self::splitParagraphs($article->body),
        ];
    }

    /** @return list<string> */
    private static function splitParagraphs(string $body): array
    {
        $paragraphs = array_map(trim(...), explode("\n\n", $body));

        return array_values(array_filter(
            $paragraphs,
            static fn (string $paragraph): bool => $paragraph !== '',
        ));
    }

    private static function formatDate(DateTimeImmutable $date): string
    {
        return sprintf(
            '%d %s %d',
            (int) $date->format('j'),
            self::MONTHS[(int) $date->format('n')],
            (int) $date->format('Y'),
        );
    }
}
