<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

trait RendersNotFoundPage
{
    private function renderNotFound(): void
    {
        http_response_code(404);
        $this->view->render('pages/errors/404.tpl', ['currentPage' => '']);
    }
}
