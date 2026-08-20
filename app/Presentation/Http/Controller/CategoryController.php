<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Presentation\Http\View\ViewRendererInterface;

final readonly class CategoryController
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
        $this->view->render('pages/categories.tpl', $this->previewData + ['currentPage' => 'categories']);
    }

    /** @param array<string, string> $parameters */
    public function show(array $parameters): void
    {
        $this->view->render('pages/category.tpl', $this->previewData + [
            'currentPage' => 'category',
            'requestedSlug' => $parameters['slug'],
        ]);
    }
}
