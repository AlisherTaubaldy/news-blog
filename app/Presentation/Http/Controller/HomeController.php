<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Presentation\Http\View\ViewRendererInterface;

final readonly class HomeController
{
    /** @param array<string, mixed> $previewData */
    public function __construct(
        private ViewRendererInterface $view,
        private array $previewData,
    ) {
    }

    /** @param array<string, string> $parameters */
    public function __invoke(array $parameters = []): void
    {
        $this->view->render('pages/home.tpl', $this->previewData + ['currentPage' => 'home']);
    }
}
