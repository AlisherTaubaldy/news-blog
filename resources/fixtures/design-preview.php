<?php

declare(strict_types=1);

$articles = [
    [
        'title' => 'Как спроектировать чистую архитектуру на PHP',
        'description' => 'Разбираем слои приложения, зависимости и границы ответственности без использования фреймворка.',
        'url' => '/preview.php?page=article',
        'image_url' => '/assets/images/preview/architecture.svg',
        'image_alt' => 'Абстрактная архитектурная композиция',
        'published_at_iso' => '2026-08-18',
        'published_at_formatted' => '18 августа 2026',
        'views' => 1284,
    ],
    [
        'title' => 'Smarty: компоненты и наследование шаблонов',
        'description' => 'Собираем переиспользуемые карточки, layout и partial-шаблоны для небольшого сайта.',
        'url' => '/preview.php?page=article',
        'image_url' => '/assets/images/preview/templates.svg',
        'image_alt' => 'Слои шаблонов интерфейса',
        'published_at_iso' => '2026-08-15',
        'published_at_formatted' => '15 августа 2026',
        'views' => 892,
    ],
    [
        'title' => 'Безопасная работа с MySQL через PDO',
        'description' => 'Подготовленные выражения, строгие типы и белые списки для надежной работы с данными.',
        'url' => '/preview.php?page=article',
        'image_url' => '/assets/images/preview/database.svg',
        'image_alt' => 'Абстрактное изображение базы данных',
        'published_at_iso' => '2026-08-11',
        'published_at_formatted' => '11 августа 2026',
        'views' => 2156,
    ],
];

$secondRow = [
    $articles[1],
    $articles[2],
    $articles[0],
];

$categories = [
    [
        'name' => 'Разработка',
        'description' => 'Архитектура и практика создания PHP-приложений.',
        'url' => '/preview.php?page=category',
        'articles' => $articles,
    ],
    [
        'name' => 'Базы данных',
        'description' => 'MySQL, проектирование схем и эффективные запросы.',
        'url' => '/preview.php?page=category',
        'articles' => $secondRow,
    ],
    [
        'name' => 'Интерфейсы',
        'description' => 'Верстка, Smarty и удобные пользовательские сценарии.',
        'url' => '/preview.php?page=category',
        'articles' => [$articles[2], $articles[0], $articles[1]],
    ],
];

return [
    'categories' => $categories,
    'category' => [
        'name' => 'Разработка',
        'description' => 'Статьи о проектировании, PHP и поддерживаемой архитектуре приложений.',
    ],
    'articles' => array_merge($articles, $secondRow),
    'article' => [
        'title' => 'Как спроектировать чистую архитектуру на PHP',
        'description' => 'Практический подход к разделению ответственности в небольшом PHP-проекте без фреймворка.',
        'image_url' => '/assets/images/preview/architecture.svg',
        'image_alt' => 'Абстрактная архитектурная композиция',
        'published_at_iso' => '2026-08-18',
        'published_at_formatted' => '18 августа 2026',
        'views' => 1285,
        'categories' => [
            ['name' => 'Разработка', 'url' => '/preview.php?page=category'],
            ['name' => 'Архитектура', 'url' => '/preview.php?page=category'],
        ],
        'paragraphs' => [
            'Даже небольшой проект становится понятнее, когда HTTP, бизнес-сценарии и работа с базой данных разделены. Контроллер принимает запрос, передает управление сервису и получает готовые данные для представления.',
            'Application service описывает конкретный пользовательский сценарий. Он зависит от интерфейсов репозиториев, поэтому детали MySQL и PDO остаются в инфраструктурном слое.',
            'Такой подход не требует сложного контейнера зависимостей. Для тестового задания достаточно явно собрать объекты в одном bootstrap-файле и передать зависимости через конструкторы.',
        ],
    ],
    'relatedArticles' => $secondRow,
    'sorting' => [
        ['label' => 'Сначала новые', 'url' => '/preview.php?page=category&sort=date', 'active' => true],
        ['label' => 'По просмотрам', 'url' => '/preview.php?page=category&sort=views', 'active' => false],
    ],
    'pagination' => [
        ['number' => 1, 'url' => '#', 'current' => true],
        ['number' => 2, 'url' => '#', 'current' => false],
        ['number' => 3, 'url' => '#', 'current' => false],
    ],
];
