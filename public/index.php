<?php

declare(strict_types=1);

use App\Infrastructure\View\SmartyViewRenderer;
use App\Presentation\Http\Controller\BlogController;
use App\Presentation\Http\Controller\CategoryController;
use App\Presentation\Http\Controller\HomeController;
use App\Presentation\Http\Routing\MethodNotAllowedException;
use App\Presentation\Http\Routing\RouteNotFoundException;
use App\Presentation\Http\Routing\Router;
use Smarty\Smarty;

require dirname(__DIR__) . '/vendor/autoload.php';

$root = dirname(__DIR__);
$smarty = new Smarty();
$smarty->setTemplateDir($root . '/resources/views');
$smarty->setCompileDir($root . '/storage/smarty/compile');
$smarty->setCacheDir($root . '/storage/smarty/cache');
$smarty->setEscapeHtml(true);

$previewData = require $root . '/resources/fixtures/design-preview.php';
$view = new SmartyViewRenderer($smarty);
$homeController = new HomeController($view, $previewData);
$blogController = new BlogController($view, $previewData);
$categoryController = new CategoryController($view, $previewData);

$router = new Router();
$router
    ->get('/', 'home', $homeController)
    ->get('/blogs', 'blogs.index', [$blogController, 'index'])
    ->get('/blogs/{slug}', 'blogs.show', [$blogController, 'show'])
    ->get('/categories', 'categories.index', [$categoryController, 'index'])
    ->get('/categories/{slug}', 'categories.show', [$categoryController, 'show']);

header('Content-Type: text/html; charset=UTF-8');

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
} catch (MethodNotAllowedException $exception) {
    http_response_code(405);
    header('Allow: ' . implode(', ', $exception->allowedMethods()));
    $view->render('pages/errors/404.tpl', $previewData + ['currentPage' => '']);
} catch (RouteNotFoundException) {
    http_response_code(404);
    $view->render('pages/errors/404.tpl', $previewData + ['currentPage' => '']);
}
