# Elem

[![Tests](https://github.com/epic-64/elem/actions/workflows/tests.yml/badge.svg)](https://github.com/epic-64/elem/actions/workflows/tests.yml)
[![Coverage](https://epic-64.github.io/elem/badges/coverage.svg)](https://epic-64.github.io/elem/coverage/index.html)
[![Lib Lines](https://epic-64.github.io/elem/badges/loc-src.svg)](https://github.com/epic-64/elem/tree/main/src)
[![Test Lines](https://epic-64.github.io/elem/badges/loc-test.svg)](https://github.com/epic-64/elem/tree/main/tests)
[![PHP](https://img.shields.io/badge/PHP-8.4+-777BB4.svg)](https://www.php.net/)
[![PHPStan](https://img.shields.io/badge/PHPStan-Level%209-brightgreen.svg)](https://phpstan.org/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Packagist](https://img.shields.io/packagist/v/epic-64/elem.svg)](https://packagist.org/packages/epic-64/elem)

> **Finally, you can be an HTML programmer.**  
> Put it on your resume and I will take you for a beer.

A fluent, type-safe PHP library for building HTML documents using the DOM.

```bash
composer require epic-64/elem
```

## Showcase

### It reads like HTML, but it's PHP

```php
div(id: 'hero', class: 'container')(
    h(1, text: 'Welcome'),
    p(text: 'Build HTML with pure PHP.'),
    div(class: 'actions')(
        a(href: '/start', class: 'btn btn-primary', text: 'Get Started'),
        a(href: '/docs', class: 'btn', text: 'Learn More')
    )
)
```

Output:

```html
<div id="hero" class="container">
    <h1>Welcome</h1>
    <p>Build HTML with pure PHP.</p>
    <div class="actions">
        <a href="/start" class="btn btn-primary">Get Started</a>
        <a href="/docs" class="btn">Learn More</a>
    </div>
</div>
```

### Components are just functions

```php
function card(string $title, string $body): Element {
    return div(class: 'card')(
        h(3, text: $title),
        p(text: $body)
    );
}

// Use it anywhere
div(class: 'grid')(
    card('Fast', 'No template parsing overhead.'),
    card('Safe', 'XSS protection built-in.'),
    card('Smart', 'Full IDE support.')
)
```

### Full power of PHP - not a crippled template language

```php
div(class: 'user-list')(
    list_of($users)
        ->filter(fn(User $u) => $u->isActive())
        ->map(fn(User $u) => userCard($u))
)
```

### Type-safe - your IDE and PHPStan catch mistakes

```php
// ❌ Blade: Typo? Runtime surprise!
<a hfer="{{ $url }}">Click</a>

// ✅ Elem: Caught before you save
a(hfer: $url)  // Error: Unknown parameter "hfer"
```

### XSS-safe by default

```php
$evil = '<script>alert("xss")</script>';
echo div(text: $evil);
// Output: <div>&lt;script&gt;alert("xss")&lt;/script&gt;</div>
```

### Layouts with slots

```php
function page(string $title, array $head = [], array $body = []): Element {
    return html(lang: 'en')(
        head()(
            title(text: $title),
            meta(charset: 'UTF-8'),
            meta(name: 'viewport', content: 'width=device-width, initial-scale=1.0'),
            ...$head
        ),
        body()(...$body)
    );
}

page('Home', 
    head: [stylesheet('/css/app.css')],
    body: [h(1, text: 'Welcome'), p(text: 'Hello!')]
);
```
Output:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Home</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <h1>Welcome</h1>
    <p>Hello!</p>
</body>
</html>
```

---

## Table of Contents

- [Installation](#installation)
- [Quick Start](#quick-start)
- [Why Elem?](#why-elem)
- [Examples](#examples)
- [Extending Elem](#extending-elem)
- [How It Works](#how-it-works)
- [API Reference](#api-reference)
- [Demo Server](#demo-server)
- [Development](#development)
- [License](#license)

## Installation

**Requirements:** PHP 8.4+, ext-dom

```bash
composer require epic-64/elem
```

## Quick Start

```php
use function Epic64\Elem\{div, p, a, span, html, head, body, title, meta, h};

// Simple elements
echo div(id: 'container', class: 'wrapper')(
    p(text: 'Hello, World!'),
    a(href: 'https://example.com', text: 'Click me')->blank(),
    span(class: 'highlight', text: 'Important')
);

// Complete HTML document
echo html(lang: 'en')(
    head()(
        meta(charset: 'UTF-8'),
        title(text: 'My Page')
    ),
    body()(
        div(id: 'app')(
            h(1, text: 'Welcome'),
            p(text: 'This is my page.')
        )
    )
);
```

## Why Elem?

- **Type-safe** - Your IDE knows what's happening. Autocomplete, refactoring, and PHPStan just work.
- **Composable** - Build reusable components as plain functions. No magic, no framework lock-in.
- **Pure PHP** - Full power of the language: loops, conditionals, functions, type hints.
- **XSS-safe** - Text is automatically escaped through the DOM.
- **LLM-friendly** - Named parameters and type checking catch AI-generated mistakes.

📖 **[Full documentation: Why Elem?](docs/why-elem.md)** *(coming soon)*

## Examples

### Basic Elements

```php
// Forms
form(action: '/login')(
    input(type: 'email', name: 'email')->required()->placeholder('Email'),
    input(type: 'password', name: 'password')->required(),
    button(text: 'Login', type: 'submit')
);

// Lists
ul(class: 'nav')(
    li(text: 'Home'),
    li(text: 'About')
);

// Tables
table()(
    tr()(th(text: 'Name'), th(text: 'Age')),
    tr()(td(text: 'Alice'), td(text: '30'))
);
```

📖 **[Full documentation: Basic Examples](docs/basic-examples.md)**

### Composition & Dynamism

Use PHP's full power: enums, typed classes, functions, and native control flow.

```php
function userCard(User $user): Element
{
    return div(class: 'user-card')(
        avatar($user->name),
        badge($user->role->value, $user->role->badge()),
        $user->active ? badge('Active', BadgeVariant::Success) : null,
    );
}

// Filter and map with full type safety
div(class: 'user-list')(
    list_of($users)
        ->filter(fn(User $u) => $u->active)
        ->map(fn(User $u) => userCard($u))
);
```

📖 **[Full documentation: Composition & Dynamism](docs/composition-and-dynamism.md)**

### Templating & Layouts

Build reusable page layouts with multiple "slots" for content injection:

```php
function dashboardLayout(
    string $pageTitle,
    array $headerSlot = [],
    array $mainSlot = [],
): Element {
    return pageLayout(
        pageTitle: $pageTitle,
        bodySlot: [
            div(class: 'dashboard')(
                el('header')(...$headerSlot),
                el('main')(...$mainSlot),
            ),
        ],
    );
}

// Fill only the slots you need
dashboardLayout(
    pageTitle: 'My Dashboard',
    headerSlot: [h(1, text: '🚀 My App')],
    mainSlot: [card('Stats', $statsContent)],
);
```

📖 **[Full documentation: Templating & Layouts](docs/templating-and-layouts.md)**

### HTMX Integration

Return HTML fragments directly from your endpoints - no JSON serialization needed:

```php
// Add HTMX attributes
button(text: 'Load More')
    ->attr('hx-get', '/api/items')
    ->attr('hx-target', '#results')
    ->attr('hx-swap', 'beforeend')

// Return HTML from your API
function handleSearch(string $query): void {
    $users = searchUsers($query);
    echo ul(class: 'search-results')(
        list_of($users)->map(fn($user) => 
            li(text: $user->name)
        )
    );
}
```

### Linking External Resources

```php
head()(
    stylesheet('/css/style.css'),
    icon('/favicon.ico'),
    font('/fonts/custom.woff2', 'font/woff2'),
    link(href: '/manifest.json', rel: 'manifest')
)
```

## How It Works

Elem is built on PHP's native [DOM extension](https://www.php.net/manual/en/book.dom.php). Each element wraps a `DOMElement`, and the `__invoke` magic method lets you add children by calling the element as a function:

```php
// This fluent syntax...
div(class: 'card')(
    h(1, text: 'Title'),
    p(text: 'Content')
);

// ...uses __invoke to append children to the DOM
```

📖 **[Full documentation: How It Works](docs/how-it-works.md)**

## Extending Elem

### Custom Elements with `el()`

Use `el()` to create any element by tag name:

```php
use function Epic64\Elem\el;

el('article', class: 'post')(...);
el('nav', class: 'main-nav')(...);
el('my-custom-component')->attr('some-prop', 'value');
```

### Custom Attributes with `->attr()`

```php
// ARIA attributes
button(text: 'Menu')
    ->attr('aria-expanded', 'false')
    ->attr('aria-controls', 'menu-panel');

// Data attributes (or use ->data())
div()->data('controller', 'dropdown');

// HTMX, Alpine.js, or any other library
div()
    ->attr('hx-get', '/api/data')
    ->attr('x-data', '{ open: false }');
```

### Raw HTML with `raw()`

When you have trusted HTML from an external source (Markdown parser, CMS, sanitizer):

```php
use function Epic64\Elem\raw;

$html = $markdownParser->convert($markdown);
div(class: 'prose')(raw($html));
```

> ⚠️ **Never use `raw()` with user input** - it bypasses XSS protection.

### Adding Text to Elements

There are three ways to add text content:

```php
use function Epic64\Elem\text;

// 1. Using the text: parameter
p(text: 'Hello, World!');

// 2. Using plain strings as children
p()('Hello, World!');

// 3. Using text() for explicit text nodes
p()(text('Hello, World!'));
```

All three methods automatically escape content for XSS protection.


## API Reference

### Element Classes

All element classes extend the base `Element` class and provide fluent interfaces:

- **Structure**: `Html`, `Head`, `Body`, `Title`, `Meta`, `Link`, `Style`, `Script`
- **Text**: `Div`, `Span`, `Paragraph`, `Heading`
- **Links & Media**: `Anchor`, `Image`
- **Forms**: `Form`, `Input`, `Button`, `Label`, `Textarea`, `Select`, `Option`
- **Lists**: `UnorderedList`, `OrderedList`, `ListItem`
- **Tables**: `Table`, `TableRow`, `TableCell`, `TableHeader`
- **Special**: `RawHtml` - Holds unescaped HTML content (use via `raw()` function)

### Common Methods

All elements support:

- `->id(string $id)` - Set the id attribute
- `->class(string ...$classes)` - Add CSS classes
- `->attr(string $name, string $value)` - Set any attribute
- `->style(string $style)` - Set inline styles
- `->data(string $name, string $value)` - Set data-* attributes
- `->toHtml(bool $pretty = false)` - Output HTML
- `->toPrettyHtml()` - Output formatted HTML (called automatically in __toString)

### Helper Functions

- `el(string $tag)` - Create a generic element with any tag name
- `raw(string $html)` - Create a `RawHtml` instance for injecting unescaped HTML
- `list_of(iterable $items)` - Create a fluent collection for mapping/filtering

## Demo Server

The `examples/` directory contains interactive demos showcasing the library's features.

### Running the Demo Server

```bash
# From the project root
php -S localhost:8080 -t examples examples/server.php
```

Then open http://localhost:8080 in your browser.

### Available Demos

- **Index** (`/`) - Overview and navigation
- **Layout Demo** (`/layout-demo`) - Complex templates with multiple slots: page layouts, dashboard layouts, cards, and modals
- **Dynamic Content Demo** (`/dynamic-content-demo`) - Showcases enums, reusable components, data transformation, and conditional rendering
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
vendor/bin/phpstan analyze
```

## License

MIT
