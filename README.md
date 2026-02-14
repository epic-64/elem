# Elem

[![Tests](https://github.com/epic-64/elem/actions/workflows/tests.yml/badge.svg)](https://github.com/epic-64/elem/actions/workflows/tests.yml)
[![Coverage](https://epic-64.github.io/elem/badges/coverage.svg)](https://epic-64.github.io/elem/coverage/index.html)
[![Lib Lines](https://epic-64.github.io/elem/badges/loc-src.svg)](https://github.com/epic-64/elem/tree/main/src)
[![Test Lines](https://epic-64.github.io/elem/badges/loc-test.svg)](https://github.com/epic-64/elem/tree/main/tests)
[![PHP](https://img.shields.io/badge/PHP-8.4+-777BB4.svg)](https://www.php.net/)
[![PHPStan](https://img.shields.io/badge/PHPStan-Level%209-brightgreen.svg)](https://phpstan.org/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Packagist](https://img.shields.io/packagist/v/epic-64/elem.svg)](https://packagist.org/packages/epic-64/elem)

A fluent, type-safe PHP library for building HTML documents using the DOM.

## Requirements

- PHP 8.4+
- ext-dom

## Installation

```bash
composer require epic-64/elem
```

## Usage

### Basic Elements

```php
use function Epic64\Elem\div;
use function Epic64\Elem\p;
use function Epic64\Elem\a;
use function Epic64\Elem\span;

// Create a simple div with text
$element = div(id: 'container', class: 'wrapper')(
    p(text: 'Hello, World!'),
    a(href: 'https://example.com', text: 'Click me')->blank(),
    span(class: 'highlight', text: 'Important')
);

echo $element->toHtml();
```

### Building a Complete HTML Document

```php
use function Epic64\Elem\html;
use function Epic64\Elem\head;
use function Epic64\Elem\body;
use function Epic64\Elem\title;
use function Epic64\Elem\meta;
use function Epic64\Elem\div;
use function Epic64\Elem\h;
use function Epic64\Elem\p;

$page = html(lang: 'en')(
    head()(
        meta(charset: 'UTF-8'),
        meta(name: 'viewport', content: 'width=device-width, initial-scale=1.0'),
        title(text: 'My Page')
    ),
    body()(
        div(id: 'app', class: 'container')(
            h(1, text: 'Welcome'),
            p(text: 'This is my page.')
        )
    )
);

echo $page;
```

### Forms

```php
use function Epic64\Elem\form;
use function Epic64\Elem\label;
use function Epic64\Elem\input;
use function Epic64\Elem\button;

$loginForm = form(id: 'login', action: '/login')(
    label(text: 'Email', for: 'email'),
    input(type: 'email', id: 'email', name: 'email')->required()->placeholder('Enter your email'),
    
    label(text: 'Password', for: 'password'),
    input(type: 'password', id: 'password', name: 'password')->required(),
    
    button(text: 'Login', type: 'submit')
);

echo $loginForm;
```

### Lists

```php
use function Epic64\Elem\ul;
use function Epic64\Elem\li;

$list = ul(class: 'nav')(
    li(text: 'Home'),
    li(text: 'About'),
    li(text: 'Contact')
);
```

### Tables

```php
use function Epic64\Elem\table;
use function Epic64\Elem\tr;
use function Epic64\Elem\th;
use function Epic64\Elem\td;

$table = table(class: 'data-table')(
    tr()(
        th(text: 'Name'),
        th(text: 'Age')
    ),
    tr()(
        td(text: 'Alice'),
        td(text: '30')
    ),
    tr()(
        td(text: 'Bob'),
        td(text: '25')
    )
);
```

### Inline Scripts

Elements with an `id` can have inline scripts that automatically receive the element:

```php
use function Epic64\Elem\form;

$form = form(id: 'my-form', action: '/submit')->script(<<<JS
    el.addEventListener('submit', (e) => {
        e.preventDefault();
        console.log('Form submitted!');
    });
JS);
```

### Using Array Results (e.g., from array_map)

```php
use function Epic64\Elem\ul;
use function Epic64\Elem\li;

$items = ['Apple', 'Banana', 'Cherry'];

$list = ul()(
    array_map(fn($item) => li(text: $item), $items)
);
```

### Linking External Resources

```php
use function Epic64\Elem\stylesheet;
use function Epic64\Elem\icon;
use function Epic64\Elem\font;
use function Epic64\Elem\link;
use function Epic64\Elem\head;

// For stylesheets, use the convenient stylesheet() helper
$head = head()(
    stylesheet('/css/style.css'),
    stylesheet('/css/theme.css')
);

// For favicons, use the icon() helper
$head = head()(
    icon('/favicon.ico'),
    icon('/icon-192.png', 'image/png')->sizes('192x192')
);

// For preloading fonts, use the font() helper
$head = head()(
    font('/fonts/custom.woff2', 'font/woff2')
);

// For other link types, use link() directly
$head = head()(
    link(href: '/manifest.json', rel: 'manifest'),
    link(href: '/feed.xml', rel: 'alternate')->type('application/rss+xml')
);
```

## Element Classes

All element classes extend the base `Element` class and provide fluent interfaces:

- **Structure**: `Html`, `Head`, `Body`, `Title`, `Meta`, `Link`, `Style`, `Script`
- **Text**: `Div`, `Span`, `Paragraph`, `Heading`
- **Links & Media**: `Anchor`, `Image`
- **Forms**: `Form`, `Input`, `Button`, `Label`, `Textarea`, `Select`, `Option`
- **Lists**: `UnorderedList`, `OrderedList`, `ListItem`
- **Tables**: `Table`, `TableRow`, `TableCell`, `TableHeader`

## Common Methods

All elements support:

- `->id(string $id)` - Set the id attribute
- `->class(string ...$classes)` - Add CSS classes
- `->attr(string $name, string $value)` - Set any attribute
- `->style(string $style)` - Set inline styles
- `->data(string $name, string $value)` - Set data-* attributes
- `->toHtml(bool $pretty = false)` - Output HTML
- `->toPrettyHtml()` - Output formatted HTML

## Demo Examples

The `examples/` directory contains interactive demos showcasing the library's features.

### Running the Demo Server

```bash
# From the project root
php -S localhost:8080 -t examples examples/server.php
```

Then open http://localhost:8080 in your browser.

### Available Demos

- **Index** (`/`) - Overview and navigation
- **Template Demo** (`/template-demo`) - Building complete HTML pages
- **HTMX Demo** (`/htmx-demo`) - Interactive components with HTMX integration

## Development

### Running Tests

```bash
# Run tests
vendor/bin/pest

# Run tests with coverage
vendor/bin/pest --coverage

# Run tests with coverage and enforce minimum threshold
vendor/bin/pest --coverage --min=80
```

### Static Analysis

```bash
vendor/bin/phpstan analyse
```

## License

MIT
