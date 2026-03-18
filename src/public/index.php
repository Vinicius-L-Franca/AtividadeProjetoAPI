<?php

require_once __DIR__ . '/../config/config.php';

// ── CORS ─────────────────────────────────────────
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

in_array($origin, $allowedOrigins) ?
    header("Access-Control-Allow-Origin: $origin") : null;
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// ── Preflight ────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ── Roteamento ───────────────────────────────────
$uri = strtok($_SERVER['REQUEST_URI'], '?');

match ($uri) {
    '/api/users'    => require __DIR__ . '/../src/api.php',
    '/docs'         => serveDocs(),
    '/openapi.json' => serveOpenApi(),
    default         => notFound(),
};

function serveDocs(): void
{
    $file = __DIR__ . '/../../views/docs.html';
    if (!file_exists($file)) {
        http_response_code(500);
        echo json_encode(['error' => 'Documentation page not found']);
        return;
    }
    header('Content-Type: text/html; charset=UTF-8');
    readfile($file);
}

function serveOpenApi(): void
{
    $file = __DIR__ . '/../../openapi.json';
    if (!file_exists($file)) {
        http_response_code(500);
        echo json_encode(['error' => 'OpenAPI specification not found']);
        return;
    }
    header('Content-Type: application/json; charset=UTF-8');
    readfile($file);
}

function notFound(): void
{
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
}