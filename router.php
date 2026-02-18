<?php
// Router for PHP built-in server so / resolves reliably in preview environments.
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = __DIR__ . $uri;

if ($uri !== '/' && is_file($file)) {
    return false; // serve static files normally
}

if ($uri === '/' || $uri === '') {
    require __DIR__ . '/index.php';
    return true;
}

$candidate = __DIR__ . '/' . ltrim($uri, '/');
if (is_file($candidate)) {
    require $candidate;
    return true;
}

$phpCandidate = $candidate . '.php';
if (is_file($phpCandidate)) {
    require $phpCandidate;
    return true;
}

http_response_code(404);
header('Content-Type: text/html; charset=UTF-8');
echo '<!doctype html><html><body style="font-family:Arial,sans-serif;padding:20px"><h1>404 Not Found</h1><p>Route not found.</p></body></html>';
