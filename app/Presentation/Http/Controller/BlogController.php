<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\ArticlePageService;
use App\Application\BlogPageService;
use App\Presentation\Http\View\ViewRendererInterface;

final readonly class BlogController
{
    use RendersNotFoundPage;

    public function __construct(
        private ViewRendererInterface $view,
        private BlogPageService $blogPageService,
        private ArticlePageService $articlePageService,
    ) {
    }

    /** @param array<string, string> $parameters */
    public function index(array $parameters = []): void
    {
        $data = $this->blogPageService->getViewData(
            is_string($_GET['sort'] ?? null) ? $_GET['sort'] : null,
            is_string($_GET['page'] ?? null) ? $_GET['page'] : null,
        );

        $this->view->render('pages/blogs.tpl', $data + ['currentPage' => 'blogs']);
    }

    /** @param array<string, string> $parameters */
    public function show(array $parameters): void
    {
        $data = $this->articlePageService->getViewData($parameters['slug']);

        if ($data === null) {
            $this->renderNotFound();

            return;
        }

        $this->view->render('pages/article.tpl', $data + ['currentPage' => 'article']);
    }
}
