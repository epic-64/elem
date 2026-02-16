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
  - [Basic Elements](#basic-elements)
  - [Composition & Dynamism](#composition--dynamism)
  - [Templating & Layouts](#templating--layouts)
  - [HTMX Integration](#htmx-integration)
  - [Linking External Resources](#linking-external-resources)
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
use Epic64\Elem\Element;
use function Epic64\Elem\div;
use function Epic64\Elem\h;

// Define a component as a simple function
function card(string $title, Element ...$content): Element {
    return div(class: 'card')(
        h(2, text: $title),
        div(class: 'card-body')(...$content)
    );
}

// Use it anywhere
echo card('Welcome',
    p(text: 'This is a card component.'),
    a(href: '/learn-more', text: 'Learn More')
);
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

### Raw HTML when you need it

Sometimes you have trusted HTML from a Markdown parser, CMS, or other source. Use `raw()` to inject it unescaped:

```php
use function Epic64\Elem\raw;
use function Epic64\Elem\div;

// Output from a Markdown parser
$htmlFromMarkdown = '<p>Hello <strong>world</strong>!</p>';

// Inject it directly into your Elem tree
echo div(class: 'content')(
    raw($htmlFromMarkdown)
);
// Output: <div class="content"><p>Hello <strong>world</strong>!</p></div>
```

> ⚠️ **Warning:** Only use `raw()` with trusted content. Never pass user input directly to `raw()` - that defeats the XSS protection!

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

### Basic Elements

Forms, lists, tables, and dynamic content generation:

```php
// Forms with validation
form(id: 'login', action: '/login')(
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

// Dynamic content from data
ul()(
    ...array_map(fn($item) => li(text: $item), $items)
);
```

📖 **[Full documentation: Basic Examples](docs/basic-examples.md)**

### Composition & Dynamism

This is where Elem really shines. Unlike templates where you're limited to template syntax, Elem gives you the full power of PHP:

- **Enums** for type-safe variants (no more `'sucess'` typos)
- **Typed classes** for your data (`User`, `Stat`, `CurrentUser`)
- **Functions** for reusable components
- **Native control flow** for conditional rendering

```php
enum BadgeVariant: string
{
    case Primary = 'primary';
    case Success = 'success';
    case Warning = 'warning';
}

function badge(string $text, BadgeVariant $variant): Element
{
    return span(class: "badge badge-{$variant->value}", text: $text);
}

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

Build reusable page layouts with multiple "slots" for injecting content into different areas. This eliminates boilerplate while keeping full flexibility.

```php
/**
 * @param string $pageTitle
 * @param list<Element> $headerSlot
 * @param list<Element> $sidebarSlot
 * @param list<Element> $mainSlot
 * @param list<Element> $footerSlot
 */
function dashboardLayout(
    string $pageTitle,
    array $headerSlot = [],
    array $sidebarSlot = [],
    array $mainSlot = [],
    array $footerSlot = [],
): Element {
    return pageLayout(
        pageTitle: $pageTitle,
        bodySlot: [
            div(class: 'dashboard')(
                el('header')(...$headerSlot),
                el('aside')(...$sidebarSlot),
                el('main')(...$mainSlot),
                el('footer')(...$footerSlot),
            ),
        ],
    );
}

// Use it - fill only the slots you need
return dashboardLayout(
    pageTitle: 'My Dashboard',
    headerSlot: [h(1, text: '🚀 My App')],
    mainSlot: [
        cardLayout(cardTitle: 'Stats', bodySlot: [...]),
        cardLayout(cardTitle: 'Activity', bodySlot: [...]),
    ],
);
```

📖 **[Full documentation: Templating & Layouts](docs/templating-and-layouts.md)**

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

Elem doesn't have every HTML element built-in (yet). Use `el()` to create any element by tag name:

```php
use function Epic64\Elem\el;

// Semantic HTML5 elements
el('article', class: 'post')(...);
el('section', id: 'hero')(...);
el('nav', class: 'main-nav')(...);
el('aside', class: 'sidebar')(...);
el('footer')(...);

// Less common elements
el('details')(
    el('summary', text: 'Click to expand'),
    p(text: 'Hidden content here')
);

el('figure')(
    img(src: '/photo.jpg', alt: 'A photo'),
    el('figcaption', text: 'Photo caption')
);

// Web components
el('my-custom-component')
    ->attr('some-prop', 'value');
```

### Custom Attributes with `->attr()`

Need an attribute that isn't covered by a fluent method? Use `->attr()`:

```php
// ARIA attributes
button(text: 'Menu')
    ->attr('aria-expanded', 'false')
    ->attr('aria-controls', 'menu-panel');

// Data attributes (or use ->data())
div()->attr('data-controller', 'dropdown');
div()->data('controller', 'dropdown');  // equivalent

// HTMX, Alpine.js, or any other library
div()
    ->attr('hx-get', '/api/data')
    ->attr('x-data', '{ open: false }')
    ->attr('@click', 'open = !open');

// Custom boolean attributes
input(type: 'text')->attr('autofocus', '');
```

### Raw HTML with `raw()`

When you have trusted HTML from an external source (Markdown parser, CMS, sanitizer):

```php
use function Epic64\Elem\raw;

// From a Markdown parser
$html = $markdownParser->convert($markdown);
div(class: 'prose')(raw($html));

// SVG icons
div(class: 'icon')(
    raw('<svg viewBox="0 0 24 24">...</svg>')
);

// Trusted third-party embed code
div(class: 'embed')(
    raw($trustedEmbedCode)
);
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

### Creating Reusable Helpers

If you use a custom element frequently, wrap it in a function:

```php
use Epic64\Elem\Element;
use function Epic64\Elem\el;

function article(?string $id = null, ?string $class = null): Element
{
    $element = el('article');
    if ($id !== null) $element->id($id);
    if ($class !== null) $element->class($class);
    return $element;
}

function section(?string $id = null, ?string $class = null): Element
{
    $element = el('section');
    if ($id !== null) $element->id($id);
    if ($class !== null) $element->class($class);
    return $element;
}

// Now use them like built-in elements
article(id: 'post-123', class: 'blog-post')(
    h(1, text: $post->title),
    section(class: 'content')(
        raw($post->htmlContent)
    )
);
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
