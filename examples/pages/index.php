<?php

declare(strict_types=1);

/**
 * Index page - List of all available examples.
 */

use Epic64\Elem\Element;
use function Epic64\Elem\{html, head, title, stylesheet, body, div, h, p, a, to_el, ul, li, el};

// Define the examples
$examples = [
    [
        'path' => '/dynamic-content-demo',
        'title' => 'Dynamic Content Demo',
        'description' => 'The real power of Elem: reusable components as functions, data transformation with map/filter, conditional rendering, and composable UI patterns.',
    ],
    [
        'path' => '/template-demo',
        'title' => 'Template Demo',
        'description' => 'A comprehensive demo showing components, user lists, navigation, and forms with HTMX & Alpine.js integration.',
    ],
    [
        'path' => '/htmx-demo',
        'title' => 'HTMX Demo',
        'description' => 'Interactive examples showcasing HTMX features: click counter, live search, validation, toggle content, polling, and infinite scroll.',
    ],
];

function example_card(string $title, string $description, string $path): Element
{
    return li(class: 'example-card')(
        a(href: $path)(
            h(2, text: $title),
            p(text: $description),
        )
    );
}

return html(lang: 'en')(
    head()(
        title(text: 'Elem Examples'),
        stylesheet(href: '/css/index.css')
    ),
    body()(
        div(class: 'container')(
            h(1)('Elem Examples'),
            p(class: 'intro')(
                'Explore examples demonstrating the Elem library - a fluent, ',
                'type-safe PHP library for building HTML documents.'
            ),
            ul(class: 'examples-list')(
                ...to_el($examples, fn($example) =>
                    example_card($example['title'], $example['description'], $example['path'])
                ),
            ),
            el('footer')(
                p()('Start the server with: '),
                el('code', text: 'php -S localhost:8080 server.php'),
            )
        )
    )
);
