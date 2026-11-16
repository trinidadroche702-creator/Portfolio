<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function database(): PDO
{
    static $connection;

    if ($connection instanceof PDO) {
        return $connection;
    }

    $settings = app_config('database');

    if (
        !is_array($settings)
        || !isset($settings['dsn'], $settings['username'])
        || !is_string($settings['dsn'])
        || !is_string($settings['username'])
    ) {
        throw new RuntimeException('Database connection settings are incomplete.');
    }

    $connection = new PDO(
        $settings['dsn'],
        $settings['username'],
        isset($settings['password']) && is_string($settings['password'])
            ? $settings['password']
            : '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    return $connection;
}
