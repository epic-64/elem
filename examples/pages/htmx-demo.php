<?php

declare(strict_types=1);

/**
 * HTMX Demo - Interactive examples showcasing HTMX features with Elem.
 *
 * Features demonstrated:
 * - Click to load
 * - Form validation with hx-post
 * - Search with hx-get
 * - Infinite scroll
 * - Polling for updates
 * - Toggle content
 */

use function Epic64\Elem\{
    html, head, title, meta, script, stylesheet, body,
    div, h, p, span, a, el,
    ul, li,
    form, label, input, button
};

// Example code snippet to display
$codeSnippet = <<<'PHP'
button(class: 'btn btn-primary', text: 'Click me')
    ->attr('hx-post', '/api/endpoint')
    ->attr('hx-target', '#result')
    ->attr('hx-swap', 'innerHTML')
PHP;

return html(lang: 'en')(
    head()(
        title(text: 'HTMX Demo - Elem Examples'),
        meta(charset: 'UTF-8'),
        meta(name: 'viewport', content: 'width=device-width, initial-scale=1.0'),
        stylesheet(href: '/css/htmx-demo.css'),
        // Highlight.js for syntax highlighting (dark theme)
        stylesheet(href: 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css'),
        script(src: 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js'),
        script(src: 'https://unpkg.com/htmx.org@2.0.4')
    ),
    body()(
        div(class: 'container')(
            a('/', class: 'back-link', text: '← Back to Examples'),

            h(1, text: 'HTMX Demo'),
            p(class: 'intro-text')(
                'This page demonstrates various HTMX features integrated with the Elem library. ',
                'All interactions below make requests to the server and update the DOM without page reloads.'
            ),

            // Code snippet showcase
            el('section', class: 'demo-section code-showcase')(
                h(2, text: 'Elem + HTMX'),
                p(text: 'Adding HTMX attributes to Elem elements is simple using the ->attr() method:'),
                div(class: 'demo-content')(
                    el('pre', class: 'code-block')(
                        el('code', class: 'language-php', text: $codeSnippet)
                    )
                )
            ),

            // Section 1: Click Counter
            el('section', class: 'demo-section')(
                h(2, text: '1. Click Counter'),
                p(text: 'A simple counter that increments on each click, demonstrating hx-post and hx-swap.'),
                div(class: 'demo-content')(
                    button(class: 'btn btn-primary counter-btn', text: 'Clicked 0 times')
                        ->attr('hx-post', '/api/counter')
                        ->attr('hx-swap', 'outerHTML')
                        ->attr('hx-vals', '{"count": 0}')
                )
            ),

            // Section 2: Live Search
            el('section', class: 'demo-section')(
                h(2, text: '2. Live Search'),
                p(text: 'Search users as you type. Uses hx-get with hx-trigger="keyup changed delay:300ms".'),
                div(class: 'demo-content')(
                    div(class: 'search-box')(
                        input(type: 'search', id: 'search', class: 'form-control', name: 'q')
                            ->attr('placeholder', 'Search users...')
                            ->attr('hx-get', '/api/search')
                            ->attr('hx-trigger', 'keyup changed delay:300ms, search')
                            ->attr('hx-target', '#search-results')
                    ),
                    div(id: 'search-results', class: 'search-results')(
                        p(class: 'hint', text: 'Start typing to search (try "alice", "bob", "admin")')
                    )
                )
            ),

            // Section 3: Email Validation
            el('section', class: 'demo-section')(
                h(2, text: '3. Inline Validation'),
                p(text: 'Real-time email validation using hx-post triggered on input change.'),
                div(class: 'demo-content')(
                    form(class: 'validation-form')(
                        div(class: 'form-group')(
                            label(text: 'Email:', for: 'email'),
                            input(type: 'email', id: 'email', class: 'form-control', name: 'email')
                                ->attr('placeholder', 'Enter email address')
                                ->attr('hx-post', '/api/validate-email')
                                ->attr('hx-trigger', 'change, keyup delay:500ms changed')
                                ->attr('hx-target', '#email-validation'),
                            span(id: 'email-validation', class: 'validation-msg')
                        ),
                        p(class: 'hint', text: 'Try: admin@example.org (taken), test@example.org (taken), or any new email')
                    )
                )
            ),

            // Section 4: Toggle Content
            el('section', class: 'demo-section')(
                h(2, text: '4. Toggle Content'),
                p(text: 'Expand/collapse content loaded from the server using hx-get.'),
                div(class: 'demo-content')(
                    div(id: 'toggle-container')(
                        div(class: 'toggle-content collapsed')(
                            button(class: 'btn btn-primary', text: 'Click to expand')
                                ->attr('hx-get', '/api/toggle?state=collapsed')
                                ->attr('hx-target', '#toggle-container')
                                ->attr('hx-swap', 'innerHTML')
                        )
                    )
                )
            ),

            // Section 5: Polling
            el('section', class: 'demo-section')(
                h(2, text: '5. Polling (Auto-refresh)'),
                p(text: 'Server time updates every 2 seconds using hx-trigger="every 2s".'),
                div(class: 'demo-content')(
                    div(id: 'time-display', class: 'time-display')
                        ->attr('hx-get', '/api/time')
                        ->attr('hx-trigger', 'load, every 2s')
                        ->attr('hx-swap', 'innerHTML')(
                            p(text: 'Loading...')
                        )
                )
            ),

            // Section 6: Infinite Scroll
            el('section', class: 'demo-section')(
                h(2, text: '6. Load More'),
                p(text: 'Click the button to load more items using hx-get and hx-swap="outerHTML".'),
                div(class: 'demo-content scroll-container')(
                    ul(id: 'items-list', class: 'items-list')(
                        li(class: 'load-trigger')(
                            button(class: 'btn btn-secondary load-more-btn', text: 'Load items')
                                ->attr('hx-get', '/api/items?page=1')
                                ->attr('hx-target', 'closest li')
                                ->attr('hx-swap', 'outerHTML')
                        )
                    )
                )
            ),

            // Footer
            el('footer', class: 'page-footer')(
                p()(
                    'Learn more about ',
                    a('https://htmx.org', text: 'HTMX', class: 'external-link')
                        ->attr('target', '_blank')
                        ->attr('rel', 'noopener'),
                    ' and check out the ',
                    a('https://htmx.org/examples/', text: 'official examples', class: 'external-link')
                        ->attr('target', '_blank')
                        ->attr('rel', 'noopener'),
                    '.'
                )
            ),

            // Initialize syntax highlighting
            script(code: 'hljs.highlightAll();')
        )
    )
);
