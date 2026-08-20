<?php

declare(strict_types=1);

namespace App\Infrastructure\View;

use App\Presentation\Http\View\ViewRendererInterface;
use Smarty\Smarty;

final readonly class SmartyViewRenderer implements ViewRendererInterface
{
    public function __construct(private Smarty $smarty)
    {
    }

    public function render(string $template, array $data = []): void
    {
        $this->smarty->assign($data);
        $this->smarty->display($template);
    }
}
