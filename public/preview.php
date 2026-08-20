<?php

declare(strict_types=1);

use Smarty\Smarty;

require dirname(__DIR__) . '/vendor/autoload.php';

$root = dirname(__DIR__);
$smarty = new Smarty();
$smarty->setTemplateDir($root . '/resources/views');
$smarty->setCompileDir($root . '/storage/smarty/compile');
$smarty->setCacheDir($root . '/storage/smarty/cache');
$smarty->setEscapeHtml(true);

$preview = require $root . '/resources/fixtures/design-preview.php';
$page = $_GET['page'] ?? 'home';
$allowedPages = ['home', 'category', 'article'];

if (!in_array($page, $allowedPages, true)) {
    $page = 'home';
}

$smarty->assign($preview);
$smarty->assign('currentPage', $page);
$smarty->display('pages/' . $page . '.tpl');
