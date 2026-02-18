<?php

declare(strict_types=1);

/**
 * Simple development web server for Elem examples.
 *
 * Usage: php -S localhost:8080 server.php
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/api/handlers.php';

use function Epic64\Elem\{html, head, title, body, div, h, p, a, stylesheet};

// Get the requested URI path
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Serve static CSS files
if (str_starts_with($uri, '/css/') && str_ends_with($uri, '.css')) {
    $cssDir = realpath(__DIR__ . '/css');
    $cssPath = realpath(__DIR__ . $uri);

    // Security: Prevent path traversal attacks
    if ($cssPath !== false && str_starts_with($cssPath, $cssDir . DIRECTORY_SEPARATOR)) {
        header('Content-Type: text/css; charset=UTF-8');
        readfile($cssPath);
        return;
    }
}

// Handle Hypermedia API routes (returns HTML fragments for HTMX)
if (str_starts_with($uri, '/api/')) {
    header('Content-Type: text/html; charset=UTF-8');

    $request = new ApiRequest(
        query: $_GET,
        body: $_POST,
    );

    if (handleApiRequest($uri, $method, $request)) {
        return;
    }

    // API: 404 for unknown API routes
    http_response_code(404);
    echo p(class: 'error', text: 'API endpoint not found');
    return;
}

$uri = $uri === '/' ? '/index' : $uri;

// Map routes to page files
$pagesDir = realpath(__DIR__ . '/pages');
$pagePath = realpath($pagesDir . $uri . '.php');

// Security: Prevent path traversal attacks - ensure resolved path is within pages directory
if ($pagePath === false || !str_starts_with($pagePath, $pagesDir . DIRECTORY_SEPARATOR)) {
    http_response_code(404);
    echo html(lang: 'en')(
        head()(
            title(text: '404 - Page Not Found'),
            stylesheet('/css/404.css')
        ),
        body()(
            div(class: 'container')(
                h(1, text: '404'),
                p(text: "Page not found"),
                a('/', text: '← Back to Examples')
            )
        )
    );
    return;
}


// Execute the page file and capture output
ob_start();
$result = require $pagePath;
$output = ob_get_clean();

header('Content-Type: text/html; charset=UTF-8');

// If the page returned content (Element or string), use that
// Otherwise, use the captured output (for raw HTML files)
if ($result !== 1 && $result !== true) {
    echo $result;
} else {
    echo $output;
}
