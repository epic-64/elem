<?php

declare(strict_types=1);


use function Epic64\Elem\html;
use function Epic64\Elem\head;
use function Epic64\Elem\body;
use function Epic64\Elem\title;
use function Epic64\Elem\meta;
use function Epic64\Elem\style;
use function Epic64\Elem\script;
use function Epic64\Elem\div;
use function Epic64\Elem\h;
use function Epic64\Elem\p;
use function Epic64\Elem\a;
use function Epic64\Elem\span;
use function Epic64\Elem\img;
use function Epic64\Elem\form;
use function Epic64\Elem\label;
use function Epic64\Elem\input;
use function Epic64\Elem\button;
use function Epic64\Elem\textarea;
use function Epic64\Elem\select;
use function Epic64\Elem\ul;
use function Epic64\Elem\ol;
use function Epic64\Elem\li;
use function Epic64\Elem\table;
use function Epic64\Elem\tr;
use function Epic64\Elem\th;
use function Epic64\Elem\td;

test('creates a complex HTML document with all major elements', function () {
    $output = html(lang: 'en')(
            head()(
                meta(charset: 'UTF-8'),
                meta(name: 'viewport', content: 'width=device-width, initial-scale=1.0'),
                meta(name: 'description', content: 'A test page for Elem'),
                title(text: 'Elem Test Page'),
                style(css: 'body { font-family: sans-serif; margin: 0; padding: 20px; }'),
                script(src: 'https://example.com/app.js')
            ),
            body(id: 'main-body', class: 'container')(
                // Header section
                div(id: 'header', class: 'header-section')(
                    h(1, id: 'main-title', class: 'title', text: 'Welcome to Elem'),
                    p(class: 'subtitle', text: 'A fluent, type-safe PHP library for building HTML documents')
                ),

                // Navigation
                div(id: 'nav', class: 'navigation')(
                    ul(class: 'nav-list')(
                        li()(a(href: '/', text: 'Home')),
                        li()(a(href: '/about', text: 'About')),
                        li()(a(href: '/contact', text: 'Contact')),
                        li()(a(href: 'https://github.com', text: 'GitHub')->blank())
                    )
                ),

                // Main content with nested elements
                div(id: 'content', class: 'main-content')(
                    div(class: 'intro')(
                        h(2, text: 'Getting Started'),
                        p(text: 'Elem makes building HTML documents easy and type-safe.'),
                        p()(
                            span(text: 'Learn more at '),
                            a(href: 'https://example.com/docs', class: 'link', text: 'our documentation'),
                            span(text: '.')
                        )
                    ),

                    // Image gallery
                    div(id: 'gallery', class: 'image-gallery')(
                        h(3, text: 'Gallery'),
                        div(class: 'gallery-grid')(
                            img(src: '/images/photo1.jpg', alt: 'Photo 1')->class('gallery-item'),
                            img(src: '/images/photo2.jpg', alt: 'Photo 2')->class('gallery-item'),
                            img(src: '/images/photo3.jpg', alt: 'Photo 3')->class('gallery-item')
                        )
                    ),

                    // Data table
                    div(id: 'data-section', class: 'data-section')(
                        h(3, text: 'User Data'),
                        table(id: 'users-table', class: 'data-table')(
                            tr(class: 'header-row')(
                                th(text: 'ID'),
                                th(text: 'Name'),
                                th(text: 'Email'),
                                th(text: 'Role')
                            ),
                            tr(class: 'data-row')(
                                td(text: '1'),
                                td(text: 'Alice Johnson'),
                                td(text: 'alice@example.com'),
                                td(text: 'Admin')
                            ),
                            tr(class: 'data-row')(
                                td(text: '2'),
                                td(text: 'Bob Smith'),
                                td(text: 'bob@example.com'),
                                td(text: 'User')
                            ),
                            tr(class: 'data-row')(
                                td(text: '3'),
                                td(text: 'Charlie Brown'),
                                td(text: 'charlie@example.com'),
                                td(text: 'Moderator')
                            )
                        )
                    ),

                    // Ordered list
                    div(class: 'features-section')(
                        h(3, text: 'Key Features'),
                        ol(class: 'features-list')(
                            li(text: 'Fluent API for building HTML'),
                            li(text: 'Type-safe element creation'),
                            li(text: 'DOM-based rendering'),
                            li(text: 'Support for inline scripts')
                        )
                    )
                ),

                // Contact form
                div(id: 'contact-section', class: 'contact-form-wrapper')(
                    h(2, text: 'Contact Us'),
                    form(id: 'contact-form', class: 'contact-form', action: '/submit-contact')(
                        div(class: 'form-group')(
                            label(text: 'Full Name', for: 'name'),
                            input(type: 'text', id: 'name', name: 'name')
                                ->required()
                                ->placeholder('Enter your full name')
                                ->class('form-control')
                        ),
                        div(class: 'form-group')(
                            label(text: 'Email Address', for: 'email'),
                            input(type: 'email', id: 'email', name: 'email')
                                ->required()
                                ->placeholder('your@email.com')
                                ->class('form-control')
                        ),
                        div(class: 'form-group')(
                            label(text: 'Subject', for: 'subject'),
                            select(id: 'subject', name: 'subject', class: 'form-control')(
                                new \Epic64\Elem\Elements\Option('general', 'General Inquiry'),
                                new \Epic64\Elem\Elements\Option('support', 'Technical Support'),
                                new \Epic64\Elem\Elements\Option('feedback', 'Feedback')
                            )
                        ),
                        div(class: 'form-group')(
                            label(text: 'Message', for: 'message'),
                            textarea(id: 'message', name: 'message', class: 'form-control')
                                ->attr('rows', '5')
                                ->attr('placeholder', 'Your message here...')
                        ),
                        div(class: 'form-actions')(
                            button(type: 'submit', class: 'btn btn-primary', text: 'Send Message'),
                            button(type: 'reset', class: 'btn btn-secondary', text: 'Clear Form')
                        )
                    )
                ),

                // Footer
                div(id: 'footer', class: 'footer')(
                    p(text: '© 2026 Elem. All rights reserved.'),
                    div(class: 'footer-links')(
                        a(href: '/privacy', text: 'Privacy Policy'),
                        span(text: ' | '),
                        a(href: '/terms', text: 'Terms of Service')
                    )
                ),

                // Inline script
                script(code: 'console.log("Page loaded");')
            )
        )->toHtml();

    // Document structure assertions
    expect($output)->toContain('<!DOCTYPE html>')
        ->and($output)->toContain('<html lang="en">')
        ->and($output)->toContain('</html>')
        ->and($output)->toContain('<meta charset="UTF-8">')
        ->and($output)->toContain('<meta name="viewport" content="width=device-width, initial-scale=1.0">')
        ->and($output)->toContain('<meta name="description" content="A test page for Elem">')
        ->and($output)->toContain('<title>Elem Test Page</title>')
        ->and($output)->toContain('<style>body { font-family: sans-serif; margin: 0; padding: 20px; }</style>')
        ->and($output)->toContain('<script src="https://example.com/app.js"></script>')
        ->and($output)->toContain('<body id="main-body" class="container">')
        ->and($output)->toContain('<div id="header" class="header-section">')
        ->and($output)->toContain('<h1 id="main-title" class="title">Welcome to Elem</h1>')
        ->and($output)->toContain('<p class="subtitle">A fluent, type-safe PHP library for building HTML documents</p>')
        ->and($output)->toContain('<div id="nav" class="navigation">')
        ->and($output)->toContain('<ul class="nav-list">')
        ->and($output)->toContain('<a href="/">Home</a>')
        ->and($output)->toContain('<a href="/about">About</a>')
        ->and($output)->toContain('<a href="https://github.com" target="_blank" rel="noopener noreferrer">GitHub</a>')
        ->and($output)->toContain('<div id="content" class="main-content">')
        ->and($output)->toContain('<h2>Getting Started</h2>')
        ->and($output)->toContain('<a href="https://example.com/docs" class="link">our documentation</a>')
        ->and($output)->toContain('<div id="gallery" class="image-gallery">')
        ->and($output)->toContain('<img src="/images/photo1.jpg" alt="Photo 1" class="gallery-item">')
        ->and($output)->toContain('<img src="/images/photo2.jpg" alt="Photo 2" class="gallery-item">')
        ->and($output)->toContain('<img src="/images/photo3.jpg" alt="Photo 3" class="gallery-item">')
        ->and($output)->toContain('<table id="users-table" class="data-table">')
        ->and($output)->toContain('<tr class="header-row">')
        ->and($output)->toContain('<th>ID</th>')
        ->and($output)->toContain('<th>Name</th>')
        ->and($output)->toContain('<th>Email</th>')
        ->and($output)->toContain('<th>Role</th>')
        ->and($output)->toContain('<td>Alice Johnson</td>')
        ->and($output)->toContain('<td>alice@example.com</td>')
        ->and($output)->toContain('<td>Admin</td>')
        ->and($output)->toContain('<td>Bob Smith</td>')
        ->and($output)->toContain('<td>Charlie Brown</td>')
        ->and($output)->toContain('<ol class="features-list">')
        ->and($output)->toContain('<li>Fluent API for building HTML</li>')
        ->and($output)->toContain('<li>Type-safe element creation</li>')
        ->and($output)->toContain('<li>DOM-based rendering</li>')
        ->and($output)->toContain('<li>Support for inline scripts</li>')
        ->and($output)->toContain('<form action="/submit-contact" method="post" id="contact-form" class="contact-form">')
        ->and($output)->toContain('<label for="name">Full Name</label>')
        ->and($output)->toContain('<input type="text" name="name" id="name" required')
        ->and($output)->toContain('placeholder="Enter your full name"')
        ->and($output)->toContain('<label for="email">Email Address</label>')
        ->and($output)->toContain('<input type="email" name="email" id="email" required')
        ->and($output)->toContain('placeholder="your@email.com"')
        ->and($output)->toContain('<select name="subject" id="subject" class="form-control">')
        ->and($output)->toContain('<option value="general">General Inquiry</option>')
        ->and($output)->toContain('<option value="support">Technical Support</option>')
        ->and($output)->toContain('<option value="feedback">Feedback</option>')
        ->and($output)->toContain('<textarea name="message" id="message" class="form-control" rows="5" placeholder="Your message here..."></textarea>')
        ->and($output)->toContain('<button type="submit" class="btn btn-primary">Send Message</button>')
        ->and($output)->toContain('<button type="reset" class="btn btn-secondary">Clear Form</button>')
        ->and($output)->toContain('<div id="footer" class="footer">')
        ->and($output)->toContain('© 2026 Elem. All rights reserved.')
        ->and($output)->toContain('<a href="/privacy">Privacy Policy</a>')
        ->and($output)->toContain('<a href="/terms">Terms of Service</a>')
        ->and($output)->toContain('<script>console.log("Page loaded");</script>');

    // Head section assertions

    // Body and container assertions

    // Header assertions

    // Navigation assertions

    // Content section assertions

    // Gallery assertions

    // Table assertions

    // Ordered list assertions

    // Form assertions

    // Footer assertions

    // Inline script assertion
});

test('creates HTML document using array_map for dynamic content', function () {
    $users = [
        ['name' => 'Alice', 'role' => 'Admin'],
        ['name' => 'Bob', 'role' => 'User'],
        ['name' => 'Charlie', 'role' => 'Moderator'],
    ];

    $output = div(id: 'user-list', class: 'users')(
        h(2, text: 'Team Members'),
        ul(class: 'user-cards')(
            array_map(
                fn($user) => li(class: 'user-card')(
                    span(class: 'user-name', text: $user['name']),
                    span(class: 'user-role', text: $user['role'])
                ),
                $users
            )
        )
    )->toHtml();

    expect($output)->toContain('<div id="user-list" class="users">')
        ->and($output)->toContain('<h2>Team Members</h2>')
        ->and($output)->toContain('<ul class="user-cards">')
        ->and($output)->toContain('<li class="user-card"><span class="user-name">Alice</span><span class="user-role">Admin</span></li>')
        ->and($output)->toContain('<li class="user-card"><span class="user-name">Bob</span><span class="user-role">User</span></li>')
        ->and($output)->toContain('<li class="user-card"><span class="user-name">Charlie</span><span class="user-role">Moderator</span></li>');
});

test('element with inline script generates correct JavaScript', function () {
    $output = form(id: 'test-form', action: '/submit')->script(<<<JS
        el.addEventListener('submit', (e) => {
            e.preventDefault();
            console.log('Form submitted!');
        });
    JS)->toHtml();

    expect($output)->toContain('<form action="/submit" method="post" id="test-form">')
        ->and($output)->toContain("const el = document.getElementById('test-form');")
        ->and($output)->toContain("el.addEventListener('submit', (e) => {")
        ->and($output)->toContain("e.preventDefault();")
        ->and($output)->toContain("console.log('Form submitted!');");
});

test('data attributes are correctly applied', function () {
    $output = div(id: 'interactive-element')
        ->data('action', 'toggle')
        ->data('target', '#modal')
        ->data('animation', 'fade')
        ->class('interactive')
        ->toHtml();

    expect($output)->toContain('id="interactive-element"')
        ->and($output)->toContain('data-action="toggle"')
        ->and($output)->toContain('data-target="#modal"')
        ->and($output)->toContain('data-animation="fade"')
        ->and($output)->toContain('class="interactive"');
});

test('inline styles are correctly applied', function () {
    $output = div(id: 'styled-box')
        ->style('background-color: #f0f0f0; padding: 20px; border-radius: 8px;')
        ->toHtml();

    expect($output)->toContain('style="background-color: #f0f0f0; padding: 20px; border-radius: 8px;"');
});

test('multiple CSS classes can be added', function () {
    $output = div(class: 'container')
        ->class('flex', 'justify-center', 'items-center')
        ->class('bg-white', 'shadow-lg')
        ->toHtml();

    expect($output)->toContain('class="container flex justify-center items-center bg-white shadow-lg"');
});

test('creates a simple document and matches the entire expected HTML string', function () {
    $output = html(lang: 'en')(
        head()(
            meta(charset: 'UTF-8'),
            title(text: 'Test Page')
        ),
        body()(
            div(id: 'app', class: 'container')(
                h(1, text: 'Hello World'),
                p(text: 'Welcome to Elem.')
            )
        )
    )->__toString();

    $expected = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
          <head>
            <meta charset="UTF-8">
            <title>
              Test Page
            </title>
          </head>
          <body>
            <div id="app" class="container">
              <h1>
                Hello World
              </h1>
              <p>
                Welcome to Elem.
              </p>
            </div>
          </body>
        </html>
        HTML;

    expect($output)->toBe($expected);
});

test('multiple documents can be created independently', function () {
    $page1 = html(lang: 'en')(
        body()(
            div(text: 'Page 1 content')
        )
    );

    $page2 = html(lang: 'de')(
        body()(
            div(text: 'Page 2 content')
        )
    );

    $output1 = $page1->toHtml();
    $output2 = $page2->toHtml();

    // Both pages should render correctly
    expect($output1)->toContain('Page 1 content')
        ->and($output1)->toContain('lang="en"')
        ->and($output1)->not->toContain('Page 2 content')
        ->and($output2)->toContain('Page 2 content')
        ->and($output2)->toContain('lang="de"')
        ->and($output2)->not->toContain('Page 1 content');

    // Re-render page 1 to confirm it wasn't affected by page 2
    $output1Again = $page1->toHtml();
    expect($output1Again)->toBe($output1);
});

