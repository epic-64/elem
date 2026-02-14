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
> Put it on your resume. We won't tell anyone.

A fluent, type-safe PHP library for building HTML documents using the DOM.

## Table of Contents

- [Installation](#installation)
- [Quick Start](#quick-start)
- [Why Elem?](#why-elem)
- [Examples](#examples)
  - [Forms](#forms)
  - [Lists](#lists)
  - [Tables](#tables)
  - [Dynamic Content](#dynamic-content)
  - [HTMX Integration](#htmx-integration)
  - [Linking External Resources](#linking-external-resources)
- [How It Works](#how-it-works)
- [API Reference](#api-reference)
  - [Element Classes](#element-classes)
  - [Common Methods](#common-methods)
- [Demo Server](#demo-server)
- [Development](#development)
- [License](#license)

## Installation

**Requirements:** PHP 8.4+, ext-dom

```bash
composer require epic-64/elem
```

## Quick Start

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

echo $element;
```

**Output:**

```html
<div id="container" class="wrapper">
    <p>Hello, World!</p>
    <a href="https://example.com" target="_blank" rel="noopener noreferrer">Click me</a>
    <span class="highlight">Important</span>
</div>
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

**Output:**

```html
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Page</title>
    </head>
    <body>
        <div id="app" class="container">
            <h1>Welcome</h1>
            <p>This is my page.</p>
        </div>
    </body>
</html>
```

## Why Elem?

### Type-safe

Your IDE knows what's happening. Autocomplete, refactoring, and static analysis just work.

```php
// ❌ Blade/Twig: Typo? You'll find out at runtime.
<a hfer="{{ $url }}">Click</a>

// ✅ Elem: PHPStan catches this before you even save.
a(hfer: $url, text: 'Click')  // Error: Unknown parameter "hfer"
```

### Composable

Build reusable components as plain functions. No magic, no framework lock-in.

```php
// Define a component as a simple function
function card(string $title): Element {
    return div(class: 'card')(
        h(2, text: $title),
        div(class: 'card-body')
    );
}

// Use it anywhere
$card = card('Welcome')(
    p(text: 'This is a card component.'),
    a(href: '/learn-more', text: 'Learn More')
);

echo $card;
```

Output:
```html
<div class="card">
    <h2>Welcome</h2>
    <div class="card-body">
        <p>This is a card component.</p>
        <a href="/learn-more">Learn More</a>
    </div>
</div>
```

### Pure PHP

No context switching. Your views are PHP, so you get the full power of the language - loops, conditionals, functions, type hints, and your IDE's full support.

```php
div(class: 'admin-list')(
    Epic64\Elem\list_of($users) # you can also use array_map() or Laravel collections
        ->filter(fn($u) => $u->isAdmin())
        ->map(fn($u) => span(class: 'badge', text: $u->name))
)
```

### XSS-safe by default

Text is automatically escaped through the DOM. Sleep better at night.

```php
$userInput = '<script>alert("hacked")</script>';

// ❌ Raw PHP: XSS vulnerability
echo "<div>$userInput</div>";

// ✅ Elem: Automatically escaped. Crisis averted.
echo div(text: $userInput);
// Output: <div>&lt;script&gt;alert("hacked")&lt;/script&gt;</div>
```

### LLM-friendly

Using AI to generate HTML? Elem's structure catches mistakes that would slip through with templates:

- **Named parameters** - No silent bugs from wrong argument order
- **Type checking** - PHPStan catches hallucinated attributes
- **No string interpolation** - Impossible to forget escaping
- **No closing tags** - Can't mismatch `<div>` with `</span>`

```php
// LLMs can't mess this up - structure is enforced, not hoped for
div(class: 'card')(
    h(2, text: $title),
    p(text: $description),
    a(href: $url, text: 'Learn more')
)
```

## Examples

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

#### Form Group Helper

Create reusable form components by defining simple helper functions. Following Elem's pattern,
the helper returns a callable element that accepts its content via `__invoke`:

```php
use Epic64\Elem\Element;
use function Epic64\Elem\div;
use function Epic64\Elem\label;

function formGroup(string $labelText, string $for): Element {
    return div(class: 'form-group')(
        label(text: $labelText, for: $for)
    );
}
```

This works because `div(...)()` returns a `Div` element, which you can invoke again to add more children.
Your custom helpers inherit the same fluent pattern.

**Full example:**

```php
use function Epic64\Elem\form;
use function Epic64\Elem\input;
use function Epic64\Elem\button;

echo form(id: 'register', action: '/register')(
    formGroup('Username', 'username')(
        input(type: 'text', id: 'username', name: 'username')
            ->required()
            ->placeholder('Choose a username')
            ->attr('pattern', '^[a-zA-Z0-9_]{3,20}$')
            ->attr('title', '3-20 characters, letters, numbers, and underscores only')
    ),
    formGroup('Email Address', 'email')(
        input(type: 'email', id: 'email', name: 'email')
            ->required()
            ->placeholder('you@example.com')
    ),
    formGroup('Phone Number', 'phone')(
        input(type: 'tel', id: 'phone', name: 'phone')
            ->placeholder('+1234567890')
            ->attr('pattern', '^\+?[0-9]{10,15}$')
            ->attr('title', '10-15 digits, optionally starting with +')
    ),
    button(text: 'Register', type: 'submit')->class('btn', 'btn-primary')
);
```

**Output:**

```html
<form action="/register" method="post" id="register">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required 
               placeholder="Choose a username"
               pattern="^[a-zA-Z0-9_]{3,20}$" 
               title="3-20 characters, letters, numbers, and underscores only">
    </div>
    <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" required 
               placeholder="you@example.com">
    </div>
    <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="tel" name="phone" id="phone" 
               placeholder="+1234567890"
               pattern="^\+?[0-9]{10,15}$" 
               title="10-15 digits, optionally starting with +">
    </div>
    <button type="submit" class="btn btn-primary">Register</button>
</form>
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

### Dynamic Content

```php
use function Epic64\Elem\ul;
use function Epic64\Elem\li;

$items = ['Apple', 'Banana', 'Cherry'];

$list = ul()(
    array_map(fn($item) => li(text: $item), $items)
);
```

### HTMX Integration

Elem is perfect for [HTMX](https://htmx.org/) - return HTML fragments directly from your endpoints, no JSON serialization needed.

**Add HTMX attributes with `->attr()`:**

```php
button(text: 'Load More')
    ->attr('hx-get', '/api/items')
    ->attr('hx-target', '#results')
    ->attr('hx-swap', 'beforeend')
```

**Return HTML fragments from your API:**

```php
// GET /api/search?q=alice
function handleSearch(string $query): void {
    $users = searchUsers($query);
    
    echo ul(class: 'search-results')(
        list_of($users)->map(fn($user) => 
            li(class: 'user-card')(
                span(class: 'name', text: $user->name),
                span(class: 'email', text: $user->email)
            )
        )
    );
}
```

This is the [Hypermedia](https://htmx.org/essays/hypermedia-apis-vs-data-apis/) approach - your server returns HTML, and HTMX swaps it into the DOM. No client-side templating, no JSON parsing, just HTML.

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

## How It Works

Elem is built on PHP's native [DOM extension](https://www.php.net/manual/en/book.dom.php), giving you rock-solid HTML generation with proper escaping and well-formed output.

### The Class Model

Every HTML element is represented by a class extending the base `Element` class. Each element wraps a `DOMElement` and provides a fluent interface for setting attributes:

```php
// Behind the scenes, div() creates a Div instance:
class Div extends Element {
    public function __construct(?string $id = null, ?string $class = null, ?string $text = null) {
        parent::__construct('div', $text);
        // ...set attributes
    }
}
```

### The `__invoke` Magic

The secret sauce is PHP's `__invoke` method. When you "call" an element like a function, you're adding children to it:

```php
// This syntax:
div(class: 'card')(
    h(1, text: 'Title'),
    p(text: 'Content')
);

// Is equivalent to:
$card = new Div(class: 'card');
$card->__invoke(
    new Heading(1, text: 'Title'),
    new Paragraph(text: 'Content')
);
```

The `__invoke` method accepts variadic arguments, so you can pass any number of children. It also handles arrays and any `iterable`, which is why `array_map()`, `list_of()`, and Laravel collections all work seamlessly:

```php
public function __invoke(DOMNode|Element|string|iterable|null ...$children): static
```

## API Reference

### Element Classes

All element classes extend the base `Element` class and provide fluent interfaces:

- **Structure**: `Html`, `Head`, `Body`, `Title`, `Meta`, `Link`, `Style`, `Script`
- **Text**: `Div`, `Span`, `Paragraph`, `Heading`
- **Links & Media**: `Anchor`, `Image`
- **Forms**: `Form`, `Input`, `Button`, `Label`, `Textarea`, `Select`, `Option`
- **Lists**: `UnorderedList`, `OrderedList`, `ListItem`
- **Tables**: `Table`, `TableRow`, `TableCell`, `TableHeader`

### Common Methods

All elements support:

- `->id(string $id)` - Set the id attribute
- `->class(string ...$classes)` - Add CSS classes
- `->attr(string $name, string $value)` - Set any attribute
- `->style(string $style)` - Set inline styles
- `->data(string $name, string $value)` - Set data-* attributes
- `->toHtml(bool $pretty = false)` - Output HTML
- `->toPrettyHtml()` - Output formatted HTML (called automatically in __toString)

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
