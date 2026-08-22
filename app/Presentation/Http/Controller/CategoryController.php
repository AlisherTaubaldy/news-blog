<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\CategoryPageService;
use App\Presentation\Http\View\ViewRendererInterface;

final readonly class CategoryController
{
    use RendersNotFoundPage;

    public function __construct(
        private ViewRendererInterface $view,
        private CategoryPageService $categoryPageService,
    ) {
    }

    /** @param array<string, string> $parameters */
    public function index(array $parameters = []): void
    {
        $data = $this->categoryPageService->getIndexViewData();

        $this->view->render('pages/categories.tpl', $data + ['currentPage' => 'categories']);
    }

    /** @param array<string, string> $parameters */
    public function show(array $parameters): void
    {
        $data = $this->categoryPageService->getShowViewData(
            $parameters['slug'],
            is_string($_GET['sort'] ?? null) ? $_GET['sort'] : null,
            is_string($_GET['page'] ?? null) ? $_GET['page'] : null,
        );

        if ($data === null) {
            $this->renderNotFound();

            return;
        }

        $this->view->render('pages/category.tpl', $data + ['currentPage' => 'category']);
    }
}
