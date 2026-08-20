<?php

declare(strict_types=1);

namespace App\Presentation\Http\Routing;

use InvalidArgumentException;

final readonly class Route
{
    /** @param callable(array<string, string>): void $handler */
    public function __construct(
        public string $method,
        public string $path,
        public string $name,
        public mixed $handler,
    ) {
        if ($path === '' || $path[0] !== '/') {
            throw new InvalidArgumentException('Route path must start with a slash.');
        }
    }

    /** @return array<string, string>|null */
    public function match(string $path): ?array
    {
        $quoted = preg_quote($this->path, '#');
        $pattern = preg_replace(
            '/\\\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\\\}/',
            '(?P<$1>[a-z0-9-]+)',
            $quoted,
        );

        if ($pattern === null || preg_match('#^' . $pattern . '$#', $path, $matches) !== 1) {
            return null;
        }

        return array_filter(
            $matches,
            static fn (string|int $key): bool => is_string($key),
            ARRAY_FILTER_USE_KEY,
        );
    }

    /** @param array<string, string|int> $parameters */
    public function generate(array $parameters = []): string
    {
        $url = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            static function (array $matches) use ($parameters): string {
                $name = $matches[1];

                if (!array_key_exists($name, $parameters)) {
                    throw new InvalidArgumentException("Missing route parameter: {$name}");
                }

                $value = (string) $parameters[$name];

                if (preg_match('/^[a-z0-9-]+$/', $value) !== 1) {
                    throw new InvalidArgumentException("Invalid route parameter: {$name}");
                }

                return rawurlencode($value);
            },
            $this->path,
        );

        return $url ?? $this->path;
    }
}
