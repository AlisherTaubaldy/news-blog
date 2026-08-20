<?php

declare(strict_types=1);

namespace App\Presentation\Http\Routing;

use InvalidArgumentException;

final class Router
{
    /** @var list<Route> */
    private array $routes = [];

    public function get(string $path, string $name, callable $handler): self
    {
        return $this->add('GET', $path, $name, $handler);
    }

    public function add(string $method, string $path, string $name, callable $handler): self
    {
        if ($this->findByName($name) !== null) {
            throw new InvalidArgumentException("Duplicate route name: {$name}");
        }

        $this->routes[] = new Route(strtoupper($method), $path, $name, $handler);

        return $this;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = $this->normalizePath((string) parse_url($uri, PHP_URL_PATH));
        $method = strtoupper($method);
        $allowedMethods = [];

        foreach ($this->routes as $route) {
            $parameters = $route->match($path);

            if ($parameters === null) {
                continue;
            }

            if ($route->method !== $method) {
                $allowedMethods[] = $route->method;
                continue;
            }

            ($route->handler)($parameters);

            return;
        }

        if ($allowedMethods !== []) {
            throw new MethodNotAllowedException(array_values(array_unique($allowedMethods)));
        }

        throw new RouteNotFoundException("Route not found: {$path}");
    }

    /** @param array<string, string|int> $parameters */
    public function url(string $name, array $parameters = []): string
    {
        $route = $this->findByName($name);

        if ($route === null) {
            throw new InvalidArgumentException("Unknown route: {$name}");
        }

        return $route->generate($parameters);
    }

    private function findByName(string $name): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->name === $name) {
                return $route;
            }
        }

        return null;
    }

    private function normalizePath(string $path): string
    {
        $decoded = rawurldecode($path);
        $normalized = '/' . trim(preg_replace('#/+#', '/', $decoded) ?? '/', '/');

        return $normalized === '/' ? '/' : rtrim($normalized, '/');
    }
}
