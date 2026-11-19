<?php

declare(strict_types=1);

ini_set('display_errors', '0');
set_exception_handler(static function (Throwable $exception): void {
    error_log($exception->__toString());
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
    }
    echo '<h1>Something went wrong</h1><p>Please try again later.</p>';
});

$configPath = dirname(__DIR__) . '/config.local.php';

if (!is_file($configPath)) {
    throw new RuntimeException(
        'Missing config.local.php. Copy config.example.php and configure the database connection.'
    );
}

$config = require $configPath;

if (!is_array($config) || !isset($config['database']) || !is_array($config['database'])) {
    throw new RuntimeException('The local configuration file is invalid.');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}

function app_config(string $key): mixed
{
    global $config;

    return $config[$key] ?? null;
}

function escape_html(string|null $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
