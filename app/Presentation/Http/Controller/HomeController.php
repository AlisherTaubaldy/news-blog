<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\HomePageService;
use App\Presentation\Http\View\ViewRendererInterface;

final readonly class HomeController
{
    public function __construct(
        private ViewRendererInterface $view,
        private HomePageService $homePageService,
    ) {
    }

    /** @param array<string, string> $parameters */
    public function __invoke(array $parameters = []): void
    {
        $this->view->render('pages/home.tpl', $this->homePageService->getViewData() + ['currentPage' => 'home']);
    }
}
