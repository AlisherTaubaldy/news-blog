<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Presentation\Http\View\ViewRendererInterface;

final readonly class BlogController
{
    /** @param array<string, mixed> $previewData */
    public function __construct(
        private ViewRendererInterface $view,
        private array $previewData,
    ) {
    }

    /** @param array<string, string> $parameters */
    public function index(array $parameters = []): void
    {
        $this->view->render('pages/blogs.tpl', $this->previewData + ['currentPage' => 'blogs']);
    }

    /** @param array<string, string> $parameters */
    public function show(array $parameters): void
    {
        $this->view->render('pages/article.tpl', $this->previewData + [
            'currentPage' => 'article',
            'requestedSlug' => $parameters['slug'],
        ]);
    }
}
