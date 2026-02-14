<?php

declare(strict_types=1);

/**
 * Hypermedia API handlers for HTMX demo endpoints.
 *
 * This is a Hypermedia API (not JSON) - all endpoints return HTML fragments
 * that HTMX swaps into the DOM. This follows the HATEOAS principle where
 * the server returns hypermedia (HTML) that drives application state.
 *
 * @see https://htmx.org/essays/hypermedia-apis-vs-data-apis/
 */

use function Epic64\Elem\{div, p, span, button, li, el};

/**
 * Simple request data object.
 */
readonly class ApiRequest
{
    /**
     * @param array<string, string> $query GET parameters
     * @param array<string, string> $body POST parameters
     */
    public function __construct(
        public array $query = [],
        public array $body = [],
    ) {}
}

/**
 * Handle API requests.
 *
 * @return bool True if the request was handled, false otherwise.
 */
function handleApiRequest(string $uri, string $method, ApiRequest $request): bool
{
    // API: Get current time
    if ($uri === '/api/time' && $method === 'GET') {
        echo div(class: 'time-display')(
            p(text: 'Current server time:'),
            el('time', class: 'time-value', text: date('H:i:s'))
        );
        return true;
    }

    // API: Search users (simulated)
    if ($uri === '/api/search' && $method === 'GET') {
        handleSearchUsers($request->query['q'] ?? '');
        return true;
    }

    // API: Click counter
    if ($uri === '/api/counter' && $method === 'POST') {
        handleCounter((int)($request->body['count'] ?? 0));
        return true;
    }

    // API: Load more items (infinite scroll simulation)
    if ($uri === '/api/items' && $method === 'GET') {
        handleInfiniteScroll((int)($request->query['page'] ?? 1));
        return true;
    }

    // API: Form validation demo
    if ($uri === '/api/validate-email' && $method === 'POST') {
        handleEmailValidation($request->body['email'] ?? '');
        return true;
    }

    // API: Toggle content
    if ($uri === '/api/toggle' && $method === 'GET') {
        handleToggle($request->query['state'] ?? 'collapsed');
        return true;
    }

    return false;
}

/**
 * Search users by name or email.
 */
function handleSearchUsers(string $query): void
{
    $query = strtolower($query);
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
}

/**
 * Handle click counter.
 */
function handleCounter(int $currentCount): void
{
    $count = $currentCount + 1;
    echo button(class: 'btn btn-primary counter-btn', text: "Clicked {$count} times")
        ->attr('hx-post', '/api/counter')
        ->attr('hx-swap', 'outerHTML')
        ->attr('hx-vals', json_encode(['count' => $count]));
}

/**
 * Handle infinite scroll items loading.
 */
function handleInfiniteScroll(int $page): void
{
    $maxPages = 3; // Limit to 3 pages for demo
    $items = [];
    $start = ($page - 1) * 5 + 1;
    $end = $start + 4;

    for ($i = $start; $i <= $end; $i++) {
        $items[] = li(class: 'list-item', text: "Item #{$i} - Loaded from server");
    }

    // Add a "load more" button for the next page, or end message
    if ($page < $maxPages) {
        $items[] = li(class: 'load-trigger')(
            button(class: 'btn btn-secondary load-more-btn', text: 'Load more items')
                ->attr('hx-get', '/api/items?page=' . ($page + 1))
                ->attr('hx-target', 'closest li')
                ->attr('hx-swap', 'outerHTML')
        );
    } else {
        $items[] = li(class: 'list-end')(
            span(class: 'hint', text: 'You\'ve reached the end!')
        );
    }

    echo implode('', array_map(fn($item) => (string)$item, $items));
}

/**
 * Handle email validation.
 */
function handleEmailValidation(string $email): void
{
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
}

/**
 * Handle toggle content.
 */
function handleToggle(string $state): void
{

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
}
