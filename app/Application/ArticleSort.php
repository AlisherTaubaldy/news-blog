<?php

declare(strict_types=1);

namespace App\Application;

enum ArticleSort: string
{
    case Date = 'date';
    case Views = 'views';
}
