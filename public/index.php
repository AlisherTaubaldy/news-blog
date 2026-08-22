<?php

declare(strict_types=1);

use App\Application\ArticlePageService;
use App\Application\BlogPageService;
use App\Application\CategoryPageService;
use App\Application\HomePageService;
use App\Infrastructure\Database\ConnectionFactory;
use App\Infrastructure\Database\PdoArticleRepository;
use App\Infrastructure\Database\PdoCategoryRepository;
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

$pdo = ConnectionFactory::createFromEnvironment();
$articleRepository = new PdoArticleRepository($pdo);
$categoryRepository = new PdoCategoryRepository($pdo);

$view = new SmartyViewRenderer($smarty);
$homeController = new HomeController($view, new HomePageService($categoryRepository, $articleRepository));
$blogController = new BlogController(
    $view,
    new BlogPageService($articleRepository),
    new ArticlePageService($articleRepository),
);
$categoryController = new CategoryController(
    $view,
    new CategoryPageService($categoryRepository, $articleRepository),
);

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
    $view->render('pages/errors/404.tpl', ['currentPage' => '']);
} catch (RouteNotFoundException) {
    http_response_code(404);
    $view->render('pages/errors/404.tpl', ['currentPage' => '']);
}
