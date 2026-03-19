<?php

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$uri = '/' . ltrim($requestPath, '/');
$uri = rtrim($uri, '/');
$uri = $uri === '' ? '/' : $uri;

$staticFile = __DIR__ . '/' . ltrim($requestPath, '/');
if ($requestPath !== '/' && is_file($staticFile)) {
    return false;
}

match ($uri) {
    '/api/users'    => require __DIR__ . '/../src/api.php',
    '/docs'         => serveDocs(),
    '/openapi'      => serveOpenApi(),
    '/openapi.json' => serveOpenApi(),
    default         => notFound(),
};

function serveDocs(): void
{
    $file = __DIR__ . '/../views/docs.html';
    if (!file_exists($file)) {
        http_response_code(404);
        echo json_encode(['error' => 'Documentation file not found']);
        return;
    }
    header('Content-Type: text/html; charset=utf-8');
    echo file_get_contents($file);
}

function serveOpenApi(): void
{
    $file = __DIR__ . '/../openapi.json';
    if (!file_exists($file)) {
        http_response_code(404);
        echo json_encode(['error' => 'OpenAPI spec not found']);
        return;
    }
    header('Content-Type: application/json; charset=utf-8');
    echo file_get_contents($file);
}

function notFound(): void
{
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
}