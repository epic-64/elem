<?php /** @noinspection PhpVariableIsUsedOnlyInClosureInspection */
/** @noinspection MultipleExpectChainableInspection */

declare(strict_types=1);


use Epic64\Elem\Elements\Div;
use Epic64\Elem\Elements\UnorderedList;
use function Epic64\Elem\a;
use function Epic64\Elem\body;
use function Epic64\Elem\button;
use function Epic64\Elem\div;
use function Epic64\Elem\el;
use function Epic64\Elem\form;
use function Epic64\Elem\h;
use function Epic64\Elem\head;
use function Epic64\Elem\html;
use function Epic64\Elem\img;
use function Epic64\Elem\input;
use function Epic64\Elem\label;
use function Epic64\Elem\li;
use function Epic64\Elem\list_of;
use function Epic64\Elem\meta;
use function Epic64\Elem\ol;
use function Epic64\Elem\p;
use function Epic64\Elem\script;
use function Epic64\Elem\select;
use function Epic64\Elem\span;
use function Epic64\Elem\style;
use function Epic64\Elem\table;
use function Epic64\Elem\td;
use function Epic64\Elem\textarea;
use function Epic64\Elem\th;
use function Epic64\Elem\title;
use function Epic64\Elem\tr;
use function Epic64\Elem\ul;

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
    expect($output)->toContain('<!DOCTYPE html>');
    expect($output)->toContain('<html lang="en">');
    expect($output)->toContain('</html>');

    // Head section assertions
    expect($output)->toContain('<meta charset="UTF-8">');
    expect($output)->toContain('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
    expect($output)->toContain('<meta name="description" content="A test page for Elem">');
    expect($output)->toContain('<title>Elem Test Page</title>');
    expect($output)->toContain('<style>body { font-family: sans-serif; margin: 0; padding: 20px; }</style>');
    expect($output)->toContain('<script src="https://example.com/app.js"></script>');

    // Body and container assertions
    expect($output)->toContain('<body id="main-body" class="container">');

    // Header assertions
    expect($output)->toContain('<div id="header" class="header-section">');
    expect($output)->toContain('<h1 id="main-title" class="title">Welcome to Elem</h1>');
    expect($output)->toContain('<p class="subtitle">A fluent, type-safe PHP library for building HTML documents</p>');

    // Navigation assertions
    expect($output)->toContain('<div id="nav" class="navigation">');
    expect($output)->toContain('<ul class="nav-list">');
    expect($output)->toContain('<a href="/">Home</a>');
    expect($output)->toContain('<a href="/about">About</a>');
    expect($output)->toContain('<a href="https://github.com" target="_blank" rel="noopener noreferrer">GitHub</a>');

    // Content section assertions
    expect($output)->toContain('<div id="content" class="main-content">');
    expect($output)->toContain('<h2>Getting Started</h2>');
    expect($output)->toContain('<a href="https://example.com/docs" class="link">our documentation</a>');

    // Gallery assertions
    expect($output)->toContain('<div id="gallery" class="image-gallery">');
    expect($output)->toContain('<img src="/images/photo1.jpg" alt="Photo 1" class="gallery-item">');
    expect($output)->toContain('<img src="/images/photo2.jpg" alt="Photo 2" class="gallery-item">');
    expect($output)->toContain('<img src="/images/photo3.jpg" alt="Photo 3" class="gallery-item">');

    // Table assertions
    expect($output)->toContain('<table id="users-table" class="data-table">');
    expect($output)->toContain('<tr class="header-row">');
    expect($output)->toContain('<th>ID</th>');
    expect($output)->toContain('<th>Name</th>');
    expect($output)->toContain('<th>Email</th>');
    expect($output)->toContain('<th>Role</th>');
    expect($output)->toContain('<td>Alice Johnson</td>');
    expect($output)->toContain('<td>alice@example.com</td>');
    expect($output)->toContain('<td>Admin</td>');
    expect($output)->toContain('<td>Bob Smith</td>');
    expect($output)->toContain('<td>Charlie Brown</td>');

    // Ordered list assertions
    expect($output)->toContain('<ol class="features-list">');
    expect($output)->toContain('<li>Fluent API for building HTML</li>');
    expect($output)->toContain('<li>Type-safe element creation</li>');
    expect($output)->toContain('<li>DOM-based rendering</li>');
    expect($output)->toContain('<li>Support for inline scripts</li>');

    // Form assertions
    expect($output)->toContain('<form action="/submit-contact" method="post" id="contact-form" class="contact-form">');
    expect($output)->toContain('<label for="name">Full Name</label>');
    expect($output)->toContain('<input type="text" name="name" id="name" required');
    expect($output)->toContain('placeholder="Enter your full name"');
    expect($output)->toContain('<label for="email">Email Address</label>');
    expect($output)->toContain('<input type="email" name="email" id="email" required');
    expect($output)->toContain('placeholder="your@email.com"');
    expect($output)->toContain('<select name="subject" id="subject" class="form-control">');
    expect($output)->toContain('<option value="general">General Inquiry</option>');
    expect($output)->toContain('<option value="support">Technical Support</option>');
    expect($output)->toContain('<option value="feedback">Feedback</option>');
    expect($output)->toContain('<textarea name="message" id="message" class="form-control" rows="5" placeholder="Your message here..."></textarea>');
    expect($output)->toContain('<button type="submit" class="btn btn-primary">Send Message</button>');
    expect($output)->toContain('<button type="reset" class="btn btn-secondary">Clear Form</button>');

    // Footer assertions
    expect($output)->toContain('<div id="footer" class="footer">');
    expect($output)->toContain('© 2026 Elem. All rights reserved.');
    expect($output)->toContain('<a href="/privacy">Privacy Policy</a>');
    expect($output)->toContain('<a href="/terms">Terms of Service</a>');

    // Inline script assertion
    expect($output)->toContain('<script>console.log("Page loaded");</script>');
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

test('special characters in text content are properly escaped', function () {
    // Test ampersand
    $element = p(text: 'HTMX & Alpine.js integration');
    expect($element->toHtml())->toContain('HTMX &amp; Alpine.js integration');

    // Test less than and greater than
    $element2 = p(text: 'Use <div> for containers');
    expect($element2->toHtml())->toContain('Use &lt;div&gt; for containers');

    // Test quotes
    $element3 = p(text: 'He said "Hello"');
    expect($element3->toHtml())->toContain('He said "Hello"');

    // Test multiple special characters together
    $element4 = div(text: 'A < B & C > D');
    expect($element4->toHtml())->toContain('A &lt; B &amp; C &gt; D');

    // Test special characters passed as children
    $element5 = div()('Tom & Jerry');
    expect($element5->toHtml())->toContain('Tom &amp; Jerry');
});

test('pre and code elements preserve whitespace in pretty-printed output', function () {
    $codeSnippet = <<<PHP
        button(class: 'btn')
            ->attr('hx-post', '/api')
            ->attr('hx-swap', 'innerHTML')
        PHP;

    $html = div(class: 'container')(
        el('pre', class: 'code-block')(
            el('code', class: 'language-php', text: <<<PHP
                button(class: 'btn')
                    ->attr('hx-post', '/api')
                    ->attr('hx-swap', 'innerHTML')
                PHP
            )
        )
    );

    $output = $html->__toString();

    // The code content should preserve its indentation (4 spaces before ->attr)
    expect($output)->toContain("    -&gt;attr('hx-post', '/api')");
    expect($output)->toContain("    -&gt;attr('hx-swap', 'innerHTML')");

    // There should be no extra whitespace between </code> and </pre>
    expect($output)->toContain('</code></pre>');

    // The pre/code tags should not have indentation added inside them
    expect($output)->not->toMatch('/<code[^>]*>\s+button/');

    // Test against the exact expected output
    $expected = <<<HTML
        <div class="container">
          <pre class="code-block">
        <code class="language-php">button(class: 'btn')
            -&gt;attr('hx-post', '/api')
            -&gt;attr('hx-swap', 'innerHTML')</code></pre>
        </div>
        HTML;

    expect($output)->toBe($expected);
});

// Additional tests for coverage

test('link element methods', function () {
    $link = \Epic64\Elem\link('/styles.css', 'stylesheet')
        ->href('/new-styles.css')
        ->rel('preload')
        ->type('text/css')
        ->media('screen')
        ->sizes('16x16');

    $output = $link->toHtml();
    expect($output)->toContain('href="/new-styles.css"')
        ->and($output)->toContain('rel="preload"')
        ->and($output)->toContain('type="text/css"')
        ->and($output)->toContain('media="screen"')
        ->and($output)->toContain('sizes="16x16"');
});

test('stylesheet helper function', function () {
    $stylesheet = \Epic64\Elem\stylesheet('/css/main.css');
    $output = $stylesheet->toHtml();
    expect($output)->toContain('href="/css/main.css"')
        ->and($output)->toContain('rel="stylesheet"');
});

test('icon helper function', function () {
    $icon = \Epic64\Elem\icon('/favicon.ico', 'image/x-icon');
    $output = $icon->toHtml();
    expect($output)->toContain('href="/favicon.ico"')
        ->and($output)->toContain('rel="icon"')
        ->and($output)->toContain('type="image/x-icon"');

    // Test without type
    $icon2 = \Epic64\Elem\icon('/favicon.png');
    $output2 = $icon2->toHtml();
    expect($output2)->toContain('href="/favicon.png"')
        ->and($output2)->toContain('rel="icon"');
});

test('font helper function', function () {
    $font = \Epic64\Elem\font('/fonts/roboto.woff2', 'font/woff2');
    $output = $font->toHtml();
    expect($output)->toContain('href="/fonts/roboto.woff2"')
        ->and($output)->toContain('rel="preload"')
        ->and($output)->toContain('as="font"')
        ->and($output)->toContain('crossorigin="anonymous"')
        ->and($output)->toContain('type="font/woff2"');

    // Test without type
    $font2 = \Epic64\Elem\font('/fonts/opensans.woff2');
    $output2 = $font2->toHtml();
    expect($output2)->toContain('rel="preload"')
        ->and($output2)->toContain('as="font"');
});

test('anchor element methods', function () {
    $anchor = a('/home')
        ->href('/new-link')
        ->target('_self');

    $output = $anchor->toHtml();
    expect($output)->toContain('href="/new-link"')
        ->and($output)->toContain('target="_self"');

    expect($anchor->getHref())->toBe('/new-link');
});

test('button element methods', function () {
    $button = button(text: 'Click me')
        ->type('submit')
        ->disabled();

    $output = $button->toHtml();
    expect($output)->toContain('type="submit"')
        ->and($output)->toContain('disabled');
});

test('form element methods', function () {
    $form = form()
        ->action('/submit')
        ->method('get');

    $output = $form->toHtml();
    expect($output)->toContain('action="/submit"')
        ->and($output)->toContain('method="get"');
});

test('image element methods', function () {
    $img = img('/photo.jpg')
        ->src('/new-photo.jpg')
        ->alt('New Photo')
        ->width(800)
        ->height(600);

    $output = $img->toHtml();
    expect($output)->toContain('src="/new-photo.jpg"')
        ->and($output)->toContain('alt="New Photo"')
        ->and($output)->toContain('width="800"')
        ->and($output)->toContain('height="600"');
});

test('input element methods', function () {
    $input = input('text')
        ->type('email')
        ->name('user_email')
        ->value('test@example.com')
        ->placeholder('Enter email')
        ->required()
        ->disabled();

    $output = $input->toHtml();
    expect($output)->toContain('type="email"')
        ->and($output)->toContain('name="user_email"')
        ->and($output)->toContain('value="test@example.com"')
        ->and($output)->toContain('placeholder="Enter email"')
        ->and($output)->toContain('required')
        ->and($output)->toContain('disabled');
});

test('script element methods', function () {
    $script = script()
        ->src('/app.js')
        ->defer()
        ->async()
        ->type('module');

    $output = $script->toHtml();
    expect($output)->toContain('src="/app.js"')
        ->and($output)->toContain('defer')
        ->and($output)->toContain('async')
        ->and($output)->toContain('type="module"');
});

test('select element methods', function () {
    $select = select()
        ->name('country')
        ->option('us', 'United States')
        ->option('uk', 'United Kingdom', true)
        ->required();

    $output = $select->toHtml();
    expect($output)->toContain('name="country"')
        ->and($output)->toContain('required')
        ->and($output)->toContain('<option value="us">United States</option>')
        ->and($output)->toContain('<option value="uk" selected>United Kingdom</option>');
});

test('textarea element methods', function () {
    $textarea = textarea()
        ->name('description')
        ->rows(10)
        ->cols(50)
        ->placeholder('Enter description')
        ->required();

    $output = $textarea->toHtml();
    expect($output)->toContain('name="description"')
        ->and($output)->toContain('rows="10"')
        ->and($output)->toContain('cols="50"')
        ->and($output)->toContain('placeholder="Enter description"')
        ->and($output)->toContain('required="required"');
});

test('table cell colspan and rowspan', function () {
    $td = td()->attr('colspan', '2')->attr('rowspan', '3');
    $th = th()->attr('colspan', '4');

    $tdCell = new \Epic64\Elem\Elements\TableCell();
    $tdCell->colspan(2)->rowspan(3);

    $thCell = new \Epic64\Elem\Elements\TableHeader();
    $thCell->colspan(4)->rowspan(2);

    expect($tdCell->toHtml())->toContain('colspan="2"')
        ->and($tdCell->toHtml())->toContain('rowspan="3"');

    expect($thCell->toHtml())->toContain('colspan="4"')
        ->and($thCell->toHtml())->toContain('rowspan="2"');
});

test('table row helper methods', function () {
    $row = tr()
        ->cell('Cell 1')
        ->cell('Cell 2')
        ->header('Header 1');

    $output = $row->toHtml();
    expect($output)->toContain('<td>Cell 1</td>')
        ->and($output)->toContain('<td>Cell 2</td>')
        ->and($output)->toContain('<th>Header 1</th>');
});

test('unordered list item method', function () {
    $ul = ul()
        ->item('Item 1')
        ->item('Item 2')
        ->item(span(text: 'Complex Item'));

    $output = $ul->toHtml();
    expect($output)->toContain('<li>Item 1</li>')
        ->and($output)->toContain('<li>Item 2</li>')
        ->and($output)->toContain('<li><span>Complex Item</span></li>');
});

test('ordered list item method', function () {
    $ol = ol()
        ->item('First')
        ->item('Second')
        ->item(a('/link', text: 'Third'));

    $output = $ol->toHtml();
    expect($output)->toContain('<li>First</li>')
        ->and($output)->toContain('<li>Second</li>')
        ->and($output)->toContain('<li><a href="/link">Third</a></li>');
});

test('html element without lang', function () {
    $html = html()(body());
    $output = $html->toHtml();
    expect($output)->toContain('<html>')
        ->and($output)->not->toContain('lang=');
});

test('meta element variations', function () {
    $meta1 = meta(charset: 'UTF-8');
    $meta2 = meta(name: 'description', content: 'Test description');
    $meta3 = meta(name: 'keywords');

    expect($meta1->toHtml())->toContain('charset="UTF-8"');
    expect($meta2->toHtml())->toContain('name="description"')
        ->and($meta2->toHtml())->toContain('content="Test description"');
    expect($meta3->toHtml())->toContain('name="keywords"');
});

test('element script method on void element', function () {
    $input = input('text', id: 'my-input')
        ->script('el.focus();');

    // When input is added to a parent, the script should follow
    $form = form()(
        $input
    );

    $output = $form->toHtml();
    expect($output)->toContain('id="my-input"')
        ->and($output)->toContain("const el = document.getElementById('my-input');")
        ->and($output)->toContain('el.focus();');
});

test('element script method requires id', function () {
    $div = div();

    expect(fn() => $div->script('console.log("test");'))
        ->toThrow(\InvalidArgumentException::class, 'Element must have an id to use script()');
});

test('element toHtml with pretty print', function () {
    $div = div(id: 'test')(
        p(text: 'Hello'),
        p(text: 'World')
    );

    $minified = $div->toHtml(pretty: false);
    $pretty = $div->toHtml(pretty: true);

    expect($minified)->not->toContain("\n");
    expect($pretty)->toContain("\n");
});

test('label element with for attribute', function () {
    $label = label(for: 'username', text: 'Username');
    $output = $label->toHtml();
    expect($output)->toContain('for="username"')
        ->and($output)->toContain('>Username</label>');
});

test('option element with selected state', function () {
    $option1 = new \Epic64\Elem\Elements\Option('val1', 'Option 1', false);
    $option2 = new \Epic64\Elem\Elements\Option('val2', 'Option 2', true);

    expect($option1->toHtml())->not->toContain('selected');
    expect($option2->toHtml())->toContain('selected');
});

test('null children are filtered out for ternary expressions', function () {
    $element = div(class: 'card')(
        h(1, text: 'Title'),
        false ? p(text: 'Optional content') : null, /** @phpstan-ignore ternary.alwaysFalse */
        p(text: 'Always shown')
    );

    $output = $element->toHtml();
    expect($output)->toContain('<h1>Title</h1>')
        ->and($output)->toContain('<p>Always shown</p>')
        ->and($output)->not->toContain('Optional content');
});

test('null values in arrays are filtered out', function () {
    $items = [
        p(text: 'First'),
        null,
        p(text: 'Third'),
    ];

    $element = div()(...$items);
    $output = $element->toHtml();

    expect($output)->toContain('<p>First</p>')
        ->and($output)->toContain('<p>Third</p>');
});

test('list_of with filter and map chain renders correctly', function () {
    $users = [
        ['name' => 'Alice', 'role' => 'admin', 'active' => true],
        ['name' => 'Bob', 'role' => 'user', 'active' => false],
        ['name' => 'Charlie', 'role' => 'admin', 'active' => true],
        ['name' => 'Diana', 'role' => 'user', 'active' => true],
    ];

    $output = ul(class: 'admin-list')(
        list_of($users)
            ->filter(fn($user) => $user['role'] === 'admin')
            ->filter(fn($user) => $user['active'])
            ->map(fn($user) => li(class: 'admin-item', text: $user['name']))
    )->toHtml();

    expect($output)
        ->toContain('<ul class="admin-list">')
        ->and($output)->toContain('<li class="admin-item">Alice</li>')
        ->and($output)->toContain('<li class="admin-item">Charlie</li>')
        ->and($output)->not->toContain('Bob')
        ->and($output)->not->toContain('Diana');
});

test('ElementsList all() and toArray() methods return underlying array', function () {
    $items = ['Apple', 'Banana', 'Cherry'];
    $list = list_of($items);

    expect($list->all())->toBe($items)
        ->and($list->toArray())->toBe($items);
});

test('ElementsList works with iterators', function () {
    $generator = function () {
        yield 'First';
        yield 'Second';
        yield 'Third';
    };

    $list = list_of($generator());
    $result = $list->map(fn($item) => li(text: $item))->all();

    expect($result)->toHaveCount(3);
    expect($result[0]->toHtml())->toContain('First');
    expect($result[1]->toHtml())->toContain('Second');
    expect($result[2]->toHtml())->toContain('Third');
});

test('Html element lang() method sets language attribute', function () {
    $html = html()->lang('de');
    $output = $html->toHtml();
    expect($output)->toContain('lang="de"');
});

test('Label element for() method sets for attribute', function () {
    $label = label(text: 'Username')->for('username_field');
    $output = $label->toHtml();
    expect($output)->toContain('for="username_field"');
});

test('Meta element fluent methods', function () {
    $meta1 = meta()->charset('UTF-16');
    $meta2 = meta()->name('author')->content('John Doe');
    $meta3 = meta()->content('Test description');

    expect($meta1->toHtml())->toContain('charset="UTF-16"');
    expect($meta2->toHtml())->toContain('name="author"')
        ->and($meta2->toHtml())->toContain('content="John Doe"');
    expect($meta3->toHtml())->toContain('content="Test description"');
});

test('element handles DOMNode children from external documents', function () {
    // Create an external DOMDocument
    $externalDom = new \DOMDocument();
    $externalNode = $externalDom->createElement('span', 'External content');

    // Add the external node as a child
    $element = div()(
        $externalNode
    );

    $output = $element->toHtml();
    expect($output)->toContain('<span>External content</span>');
});

test('element handles DOMNode children from same document', function () {
    // Create a node from the same factory DOM
    $dom = \Epic64\Elem\ElementFactory::dom();
    $sameDocNode = $dom->createElement('span', 'Same doc content');

    $element = div()(
        $sameDocNode
    );

    $output = $element->toHtml();
    expect($output)->toContain('<span>Same doc content</span>');
});

test('void element script is inserted after element when element has parent', function () {
    // Create input with parent first, then add script
    $form = form(id: 'test-form');
    $input = input('text', id: 'my-text-input');

    // Add input to form first
    $form($input);

    // Now add script to input (which already has a parent)
    $input->script('el.value = "test";');

    $output = $form->toHtml();
    expect($output)->toContain('id="my-text-input"')
        ->and($output)->toContain("const el = document.getElementById('my-text-input');")
        ->and($output)->toContain('el.value = "test";');
});

test('pretty print handles empty HTML gracefully', function () {
    $element = div();
    // Create a minimal element and test the pretty print path
    $output = $element->toPrettyHtml();
    expect($output)->toBe("<div>\n</div>");
});

test('element getAttr returns attribute value', function () {
    $div = div(id: 'test-div', class: 'my-class');
    expect($div->getAttr('id'))->toBe('test-div')
        ->and($div->getAttr('class'))->toBe('my-class')
        ->and($div->getAttr('nonexistent'))->toBe('');
});

test('raw HTML string passed as child is escaped', function () {
    // todo: make up mind if we want the security, or the flexibility of allowing raw HTML.
    //   For now, we will escape it to prevent XSS vulnerabilities.

    $rawHtml = '<strong>Bold</strong>';

    $element = div()($rawHtml);
    $output = $element->toHtml();

    // The raw HTML should be escaped, not rendered as actual HTML
    expect($output)->toContain('&lt;strong&gt;Bold&lt;/strong&gt;')
        ->and($output)->not->toContain('<strong>Bold</strong>');
});

test('raw() function allows unescaped HTML injection', function () {
    $rawHtml = '<strong>Bold</strong>';

    $element = div()(\Epic64\Elem\raw($rawHtml));
    $output = $element->toHtml();

    // The raw HTML should NOT be escaped
    expect($output)->toContain('<strong>Bold</strong>')
        ->and($output)->not->toContain('&lt;strong&gt;');
});

test('raw() function works with complex HTML', function () {
    $complexHtml = '<div class="inner"><span id="test">Hello</span><br/></div>';

    $element = div(class: 'outer')(\Epic64\Elem\raw($complexHtml));
    $output = $element->toHtml();

    expect($output)->toContain('<div class="inner">')
        ->and($output)->toContain('<span id="test">Hello</span>')
        ->and($output)->toContain('<br>');
});

test('raw() function can be mixed with regular elements', function () {
    $element = div()(
        p(text: 'Regular paragraph'),
        \Epic64\Elem\raw('<strong>Raw bold</strong>'),
        span(text: 'Regular span')
    );
    $output = $element->toHtml();

    expect($output)->toContain('<p>Regular paragraph</p>')
        ->and($output)->toContain('<strong>Raw bold</strong>')
        ->and($output)->toContain('<span>Regular span</span>');
});

test('raw() function with empty string produces no output', function () {
    $element = div()(\Epic64\Elem\raw(''));
    $output = $element->toHtml();

    expect($output)->toBe('<div></div>');
});

test('RawHtml __toString returns the HTML content', function () {
    $raw = new \Epic64\Elem\Elements\RawHtml('<strong>Test</strong>');
    expect((string) $raw)->toBe('<strong>Test</strong>');
});

test('text() function creates escaped text node', function () {
    $element = div()(
        \Epic64\Elem\text('Hello, '),
        span(text: 'World'),
        \Epic64\Elem\text('!')
    );
    $output = $element->toHtml();

    expect($output)->toBe('<div>Hello, <span>World</span>!</div>');
});

test('text() function escapes HTML special characters', function () {
    $element = div()(\Epic64\Elem\text('<script>alert("XSS")</script>'));
    $output = $element->toHtml();

    expect($output)->toBe('<div>&lt;script&gt;alert("XSS")&lt;/script&gt;</div>');
});

test('text() function with empty string produces no output', function () {
    $element = div()(\Epic64\Elem\text(''));
    $output = $element->toHtml();

    expect($output)->toBe('<div></div>');
});

test('Text __toString returns escaped HTML content', function () {
    $text = new \Epic64\Elem\Elements\Text('<strong>Test</strong>');
    expect((string) $text)->toBe('&lt;strong&gt;Test&lt;/strong&gt;');
});

test('constructor params, fluent methods, and attr() are all equivalent', function () {
    // ex1: Named parameters in constructor
    $ex1 = div(id: 'my-div', class: 'my-content')(
        'child elements go here'
    );

    // ex2: Fluent method chaining with dedicated methods
    $ex2 = div()->id('my-div')->class('my-content')(
        'child elements go here'
    );

    // ex3: Fluent method chaining with generic attr() method
    $ex3 = div()->attr('id', 'my-div')->attr('class', 'my-content')(
        'child elements go here'
    );

    $expected = '<div id="my-div" class="my-content">child elements go here</div>';

    expect($ex1->toHtml())->toBe($expected)
        ->and($ex2->toHtml())->toBe($expected)
        ->and($ex3->toHtml())->toBe($expected);
});

test('tap() method allows imperative modifications', function () {
    $element = div(class: 'card')->tap(function ($el) {
        $el->class('highlighted');
        $el->data('loaded', 'true');
    });

    $output = $element->toHtml();

    expect($output)->toBe('<div class="card highlighted" data-loaded="true"></div>');
});

test('tap() method returns the element for chaining', function () {
    $element = div(id: 'test')
        ->tap(fn($el) => $el->class('first'))
        ->tap(fn($el) => $el->class('second'))
        ->attr('title', 'My Element');

    $output = $element->toHtml();

    expect($output)->toBe('<div id="test" class="first second" title="My Element"></div>');
});

test('tap() method with conditionals and loops', function () {
    $isAdmin = true;
    $displayNotifications = false;
    $notifications = ['Notification 1', 'Notification 2'];

    $element = div(class: 'user-card')->tap(function (Div $el) use ($isAdmin, $displayNotifications, $notifications) {
        if ($isAdmin) { /** @phpstan-ignore if.alwaysTrue */
            $el->class('admin');
            $el->data('role', 'administrator');
        }

        if ($displayNotifications) { /** @phpstan-ignore if.alwaysFalse */
            foreach ($notifications as $note) {
                $el->append(div(class: 'notification', text: $note));
            }
        }
    });

    $output = $element->toHtml();

    expect($output)->toBe('<div class="user-card admin" data-role="administrator"></div>');
});

test('tap() with foreach produces same result as functional map', function () {
    $permissions = ['read', 'write', 'delete'];

    // Imperative approach using tap() and foreach
    $imperative = ul()->tap(function (UnorderedList $el) use ($permissions) {
        foreach ($permissions as $perm) {
            $el->append(li(class: 'permission', text: $perm));
        }
    });

    // Functional approach using map
    $functional = ul()(
        array_map(fn($perm) => li(class: 'permission', text: $perm), $permissions)
    );

    $expected = '<ul>'
        . '<li class="permission">read</li>'
        . '<li class="permission">write</li>'
        . '<li class="permission">delete</li>'
        . '</ul>';

    expect($imperative->toHtml())->toBe($expected)
        ->and($functional->toHtml())->toBe($expected);
});

test('when() method executes callback only when condition is true', function () {
    $elementTrue = div(class: 'card')
        ->when(true, fn($el) => $el->class('admin'));

    $elementFalse = div(class: 'card')
        ->when(false, fn($el) => $el->class('admin'));

    expect($elementTrue->toHtml())->toBe('<div class="card admin"></div>')
        ->and($elementFalse->toHtml())->toBe('<div class="card"></div>');
});

test('when() method can be chained multiple times', function () {
    $isAdmin = true;
    $isActive = false;
    $isPremium = true;

    $element = div(class: 'user')
        ->when($isAdmin, fn($el) => $el->class('admin'))
        ->when($isActive, fn($el) => $el->class('active'))
        ->when($isPremium, fn($el) => $el->class('premium'));

    expect($element->toHtml())->toBe('<div class="user admin premium"></div>');
});

test('when() replaces tap() with conditional', function () {
    $isAdmin = true;

    // Using tap() with if
    $withTap = div(class: 'card')->tap(function ($el) use ($isAdmin) {
        if ($isAdmin) { /** @phpstan-ignore if.alwaysTrue */
            $el->class('admin');
            $el->data('role', 'administrator');
        }
    });

    // Using when() - cleaner for simple conditions
    $withWhen = div(class: 'card')
        ->when($isAdmin, fn($el) => $el->class('admin')->data('role', 'administrator'));

    $expected = '<div class="card admin" data-role="administrator"></div>';

    expect($withTap->toHtml())->toBe($expected)
        ->and($withWhen->toHtml())->toBe($expected);
});

