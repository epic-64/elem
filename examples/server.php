<?php

declare(strict_types=1);

/**
 * Simple development web server for Elem examples.
 *
 * Usage: php -S localhost:8080 server.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use function Epic64\Elem\{html, head, title, body, div, h, p, a, stylesheet, el, span, button, li};

// Get the requested URI path
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Serve static CSS files
if (str_starts_with($uri, '/css/') && str_ends_with($uri, '.css')) {
    $cssPath = __DIR__ . $uri;
    if (file_exists($cssPath)) {
        header('Content-Type: text/css; charset=UTF-8');
        readfile($cssPath);
        return;
    }
}

// Simple API routes for HTMX demos
if (str_starts_with($uri, '/api/')) {
    header('Content-Type: text/html; charset=UTF-8');

    // API: Get current time
    if ($uri === '/api/time' && $method === 'GET') {
        echo div(class: 'time-display')(
            p(text: 'Current server time:'),
            el('time', class: 'time-value', text: date('H:i:s'))
        );
        return;
    }

    // API: Search users (simulated)
    if ($uri === '/api/search' && $method === 'GET') {
        $query = strtolower($_GET['q'] ?? '');
        $users = [
            ['name' => 'Alice Johnson', 'email' => 'alice@example.org', 'role' => 'Admin'],
            ['name' => 'Bob Smith', 'email' => 'bob@example.org', 'role' => 'User'],
            ['name' => 'Charlie Brown', 'email' => 'charlie@example.org', 'role' => 'User'],
            ['name' => 'Diana Prince', 'email' => 'diana@example.org', 'role' => 'Moderator'],
            ['name' => 'Eve Wilson', 'email' => 'eve@example.org', 'role' => 'User'],
        ];

        $filtered = array_filter($users, fn($u) =>
            empty($query) || str_contains(strtolower($u['name']), $query) || str_contains(strtolower($u['email']), $query)
        );

        if (empty($filtered)) {
            echo p(class: 'no-results', text: 'No users found matching "' . htmlspecialchars($query) . '"');
            return;
        }

        echo div(class: 'search-results')(
            ...array_map(
                fn($user) => div(class: 'result-item')(
                    span(class: 'user-name', text: $user['name']),
                    span(class: 'user-email', text: $user['email']),
                    span(class: 'user-role badge', text: $user['role'])
                ),
                $filtered
            )
        );
        return;
    }

    // API: Click counter
    if ($uri === '/api/counter' && $method === 'POST') {
        $count = (int)($_POST['count'] ?? 0) + 1;
        echo button(class: 'btn btn-primary counter-btn', text: "Clicked {$count} times")
            ->attr('hx-post', '/api/counter')
            ->attr('hx-swap', 'outerHTML')
            ->attr('hx-vals', json_encode(['count' => $count]));
        return;
    }

    // API: Load more items (infinite scroll simulation)
    if ($uri === '/api/items' && $method === 'GET') {
        $page = (int)($_GET['page'] ?? 1);
        $items = [];
        $start = ($page - 1) * 5 + 1;
        $end = $start + 4;

        for ($i = $start; $i <= $end; $i++) {
            $items[] = li(class: 'list-item', text: "Item #{$i} - Loaded from server");
        }

        // Add a "load more" trigger for the next page
        if ($page < 3) { // Limit to 3 pages for demo
            $items[] = li(id: 'load-more', class: 'load-trigger')
                ->attr('hx-get', '/api/items?page=' . ($page + 1))
                ->attr('hx-trigger', 'revealed')
                ->attr('hx-swap', 'outerHTML')(
                    span(class: 'loading', text: 'Loading more...')
                );
        }

        echo implode('', array_map(fn($item) => (string)$item, $items));
        return;
    }

    // API: Form validation demo
    if ($uri === '/api/validate-email' && $method === 'POST') {
        $email = $_POST['email'] ?? '';
        $takenEmails = ['admin@example.org', 'test@example.org', 'user@example.org'];

        if (empty($email)) {
            echo span(class: 'validation-msg warning', text: 'Please enter an email');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo span(class: 'validation-msg error', text: 'Invalid email format');
        } elseif (in_array($email, $takenEmails)) {
            echo span(class: 'validation-msg error', text: 'This email is already taken');
        } else {
            echo span(class: 'validation-msg success', text: '✓ Email is available');
        }
        return;
    }

    // API: Toggle content
    if ($uri === '/api/toggle' && $method === 'GET') {
        $state = $_GET['state'] ?? 'collapsed';

        if ($state === 'collapsed') {
            echo div(class: 'toggle-content expanded')(
                p(text: 'This is the expanded content! It was loaded dynamically via HTMX.'),
                p(text: 'HTMX makes it easy to add interactivity without writing JavaScript.'),
                button(class: 'btn btn-secondary', text: 'Collapse')
                    ->attr('hx-get', '/api/toggle?state=expanded')
                    ->attr('hx-target', '#toggle-container')
                    ->attr('hx-swap', 'innerHTML')
            );
        } else {
            echo div(class: 'toggle-content collapsed')(
                button(class: 'btn btn-primary', text: 'Click to expand')
                    ->attr('hx-get', '/api/toggle?state=collapsed')
                    ->attr('hx-target', '#toggle-container')
                    ->attr('hx-swap', 'innerHTML')
            );
        }
        return;
    }

    // API: 404 for unknown API routes
    http_response_code(404);
    echo p(class: 'error', text: 'API endpoint not found');
    return;
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
