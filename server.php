<?php

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');

$publicPath = __DIR__ . '/public';
$requestedFile = $publicPath . $uri;

// Let PHP's built-in server serve only real files directly so missing assets
// continue through Laravel and correctly return a 404 during local development.
if ($uri !== '/' && is_file($requestedFile)) {
    return false;
}

require_once $publicPath . '/index.php';
