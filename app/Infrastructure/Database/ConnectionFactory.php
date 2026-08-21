<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use PDO;
use RuntimeException;

final class ConnectionFactory
{
    public static function createFromEnvironment(): PDO
    {
        $host = self::required('DB_HOST');
        $database = self::required('DB_NAME');
        $user = self::required('DB_USER');
        $password = self::required('DB_PASSWORD');
        $port = filter_var(getenv('DB_PORT') ?: '3306', FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 65535],
        ]);

        if ($port === false) {
            throw new RuntimeException('DB_PORT must be a valid TCP port.');
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $host,
            $port,
            $database,
        );

        return new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ]);
    }

    private static function required(string $name): string
    {
        $value = getenv($name);

        if ($value === false || trim($value) === '') {
            throw new RuntimeException("Required environment variable {$name} is missing.");
        }

        return $value;
    }
}
