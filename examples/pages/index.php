<?php

declare(strict_types=1);

/**
 * Index page - List of all available examples.
 */

use function Epic64\Elem\{html, head, title, stylesheet, body, div, h, p, a, ul, li, el};

// Define the examples
$examples = [
    [
        'path' => '/template-demo',
        'title' => 'Template Demo',
        'description' => 'A comprehensive demo showing components, user lists, navigation, and forms with HTMX & Alpine.js integration.',
    ],
];

return html(lang: 'en')(
    head()(
        title(text: 'Elem Examples'),
        stylesheet(href: '/css/index.css')
    ),
    body()(
        div(class: 'container')(
            h(1, text: 'Elem Examples'),
            p(class: 'intro', text: 'Explore examples demonstrating the Elem library - a fluent, type-safe PHP library for building HTML documents.'),

            ul(class: 'examples-list')(
                ...array_map(
                    fn($example) => li(class: 'example-card')(
                        a($example['path'])(
                            h(2, text: $example['title']),
                            p(text: $example['description']),
                            div(class: 'arrow', text: '→')
                        )
                    ),
                    $examples
                )
            ),

            el('footer')(
                p(text: 'Start the server with: '),
                el('code', text: 'php -S localhost:8080 server.php'),
            )
        )
    )
);
