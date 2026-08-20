<?php

declare(strict_types=1);

namespace App\Presentation\Http\View;

interface ViewRendererInterface
{
    /** @param array<string, mixed> $data */
    public function render(string $template, array $data = []): void;
}
