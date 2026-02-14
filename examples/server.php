<?php

declare(strict_types=1);

/**
 * Simple development web server for Elem examples.
 *
 * Usage: php -S localhost:8080 server.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use function Epic64\Elem\{html, head, title, body, div, h, p, a, stylesheet};

// Get the requested URI path
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static CSS files
if (str_starts_with($uri, '/css/') && str_ends_with($uri, '.css')) {
    $cssPath = __DIR__ . $uri;
    if (file_exists($cssPath)) {
        header('Content-Type: text/css; charset=UTF-8');
        readfile($cssPath);
        return;
    }
}

$uri = $uri === '/' ? '/index' : $uri;

// Map routes to page files
$pagesDir = __DIR__ . '/pages';
$pagePath = $pagesDir . $uri . '.php';

if (!file_exists($pagePath)) {
    echo html(lang: 'en')(
        head()(
            title(text: '404 - Page Not Found'),
            stylesheet('/css/404.css')
        ),
        body()(
            div(class: 'container')(
                h(1, text: '404'),
                p(text: "Page not found: $uri"),
                a('/', text: '← Back to Examples')
            )
        )
    );
}


// Execute the page file and capture output
ob_start();
$result = require $pagePath;
$output = ob_get_clean();

header('Content-Type: text/html; charset=UTF-8');
echo $result;
