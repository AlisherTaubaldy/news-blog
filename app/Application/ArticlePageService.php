<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Repository\ArticleRepositoryInterface;

final readonly class ArticlePageService
{
    private const RELATED_LIMIT = 3;

    public function __construct(private ArticleRepositoryInterface $articles)
    {
    }

    /** @return array<string, mixed>|null */
    public function getViewData(string $slug): ?array
    {
        $article = $this->articles->findBySlug($slug);

        if ($article === null) {
            return null;
        }

        $this->articles->incrementViews($article->id);
        $related = $this->articles->findRelated($article->id, self::RELATED_LIMIT);

        return [
            'article' => ArticlePresenter::toDetail($article),
            'relatedArticles' => array_map(ArticlePresenter::toSummary(...), $related),
        ];
    }
}
