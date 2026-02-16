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
  - [Composition & Dynamism](#composition--dynamism)
  - [Templating & Layouts](#templating--layouts)
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

### Composition & Dynamism

This is where Elem really shines. Unlike templates where you're limited to template syntax, Elem gives you the full power of PHP: enums for type-safe variants, functions for reusable components, and native control flow for conditional rendering.

#### Type-Safe Variants with Enums

No more typos like `'sucess'` or `'waning'` - the compiler catches them:

```php
use Epic64\Elem\Element;
use function Epic64\Elem\span;
use function Epic64\Elem\div;

enum BadgeVariant: string
{
    case Default = 'default';
    case Primary = 'primary';
    case Success = 'success';
    case Warning = 'warning';
    case Error = 'error';
}

enum AlertType: string
{
    case Info = 'info';
    case Success = 'success';
    case Warning = 'warning';
    case Error = 'error';

    public function icon(): string
    {
        return match ($this) {
            self::Info => 'ℹ️',
            self::Success => '✅',
            self::Warning => '⚠️',
            self::Error => '❌',
        };
    }
}

function badge(string $text, BadgeVariant $variant = BadgeVariant::Default): Element
{
    return span(class: "badge badge-{$variant->value}", text: $text);
}

function alert(string $message, AlertType $type = AlertType::Info): Element
{
    return div(class: "alert alert-{$type->value}")(
        span(class: 'alert-icon', text: $type->icon()),
        span(class: 'alert-message', text: $message)
    );
}

// IDE autocomplete shows you all valid options
badge('Active', BadgeVariant::Success);
alert('Something went wrong!', AlertType::Error);
```

#### Composable Components

Components are just functions. Nest them, parameterize them, compose them:

```php
use Epic64\Elem\Element;
use function Epic64\Elem\div;
use function Epic64\Elem\span;
use function Epic64\Elem\h;

enum Trend: string
{
    case Up = 'up';
    case Down = 'down';
    case Neutral = 'neutral';

    public function icon(): string
    {
        return match ($this) {
            self::Up => '📈',
            self::Down => '📉',
            self::Neutral => '➡️',
        };
    }
}

readonly class User
{
    public function __construct(
        public string $name,
        public string $email,
        public UserRole $role,
        public bool $active,
    ) {}
}

readonly class Stat
{
    public function __construct(
        public string $label,
        public int $value,
        public Trend $trend,
    ) {}
}

function avatar(string $name): Element
{
    $initials = implode('', array_map(
        fn($word) => mb_substr($word, 0, 1),
        array_slice(explode(' ', $name), 0, 2)
    ));

    return div(class: 'avatar', text: strtoupper($initials));
}

function userCard(User $user): Element
{
    return div(class: 'user-card')(
        avatar($user->name),
        div(class: 'user-info')(
            span(class: 'user-name', text: $user->name),
            span(class: 'user-email', text: $user->email)
        ),
        badge($user->role->value, $user->role->badge()),
        badge($user->active ? 'Active' : 'Inactive', 
              $user->active ? BadgeVariant::Success : BadgeVariant::Error)
    );
}

function statCard(Stat $stat): Element
{
    return div(class: "stat-card stat-{$stat->trend->value}")(
        span(class: 'stat-value', text: (string) $stat->value),
        span(class: 'stat-label', text: $stat->label),
        span(class: 'stat-trend', text: $stat->trend->icon())
    );
}
```

#### Data Transformation

Filter, map, and transform your data with native PHP or the fluent `list_of()` helper:

```php
use function Epic64\Elem\div;
use function Epic64\Elem\list_of;

enum UserRole: string
{
    case Admin = 'admin';
    case Editor = 'editor';
    case Viewer = 'viewer';

    public function badge(): BadgeVariant
    {
        return match ($this) {
            self::Admin => BadgeVariant::Primary,
            self::Editor => BadgeVariant::Warning,
            self::Viewer => BadgeVariant::Default,
        };
    }
}

/** @var list<User> $users */
$users = [
    new User('Alice', 'alice@example.org', UserRole::Admin, active: true),
    new User('Bob', 'bob@example.org', UserRole::Editor, active: true),
    new User('Charlie', 'charlie@example.org', UserRole::Viewer, active: false),
];

// Show only active users
div(class: 'active-users')(
    list_of($users)
        ->filter(fn(User $user) => $user->active)
        ->map(fn(User $user) => userCard($user))
);

// Filter by role - no typos possible!
div(class: 'admin-users')(
    list_of($users)
        ->filter(fn(User $u) => $u->role === UserRole::Admin)
        ->map(fn(User $u) => userCard($u))
);
```

#### Conditional Rendering

It's just PHP - use ternaries, if statements, or match expressions:

```php
use function Epic64\Elem\div;
use function Epic64\Elem\p;

readonly class CurrentUser
{
    public function __construct(
        public string $name,
        public UserRole $role,
        public int $notifications,
    ) {}

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }
}

$currentUser = new CurrentUser('Jane', UserRole::Admin, notifications: 3);
$outOfStockCount = count(array_filter($products, fn(Product $p) => !$p->inStock));

div(class: 'dashboard')(
    // Conditional badge
    $currentUser->notifications > 0
        ? badge("{$currentUser->notifications} new", BadgeVariant::Warning) 
        : null,

    // Admin-only content - method encapsulates the logic
    $currentUser->isAdmin() ? div(class: 'admin-panel')(
        alert('You have admin privileges.', AlertType::Warning),
        p(text: 'Manage users, view logs, configure settings.')
    ) : null,

    // Dynamic alerts based on data
    $outOfStockCount > 0
        ? alert("$outOfStockCount product(s) out of stock", AlertType::Warning)
        : alert('All products in stock!', AlertType::Success),

    // Match on enum - exhaustive checking by PHPStan
    match ($currentUser->role) {
        UserRole::Admin => badge('Administrator', BadgeVariant::Primary),
        UserRole::Editor => badge('Editor', BadgeVariant::Warning),
        UserRole::Viewer => badge('Viewer', BadgeVariant::Default),
    }
);
```

#### Putting It All Together

Here's an example combining all these patterns:

```php
use function Epic64\Elem\{html, head, title, body, div, h, list_of, stylesheet};

$currentUser = new CurrentUser('Jane', UserRole::Admin, notifications: 3);

/** @var list<User> $users */
$users = $userRepository->findAll();

/** @var list<Stat> $stats */
$stats = [
    new Stat('Total Users', count($users), Trend::Up),
    new Stat('Active', count(array_filter($users, fn(User $u) => $u->active)), Trend::Up),
];

return html(lang: 'en')(
    head()(
        title(text: 'Dashboard'),
        stylesheet('/css/app.css')
    ),
    body()(
        div(class: 'dashboard')(
            // Header with conditional notification badge
            div(class: 'header')(
                h(1, text: "Welcome back, {$currentUser->name}!"),
                $currentUser->notifications > 0
                    ? badge("{$currentUser->notifications} new", BadgeVariant::Warning)
                    : null
            ),

            // Admin alert - type-safe method call
            $currentUser->isAdmin()
                ? alert('Admin mode enabled', AlertType::Info)
                : null,

            // Stats grid - fully typed Stat objects
            div(class: 'stats-grid')(
                ...array_map(fn(Stat $s) => statCard($s), $stats)
            ),

            // User list with filtering - typed throughout
            div(class: 'user-list')(
                h(2, text: 'Active Team Members'),
                list_of($users)
                    ->filter(fn(User $u) => $u->active)
                    ->map(fn(User $u) => userCard($u))
            )
        )
    )
);
```

This is the power of Elem: **your views are PHP**, so you get type safety, IDE support, refactoring, and the full expressiveness of the language. No template DSL to learn, no magic strings, no runtime surprises.

### Templating & Layouts

Build reusable page layouts with multiple "slots" for injecting content into different areas (head, sidebar, main, footer). This eliminates boilerplate while keeping full flexibility.

#### Base Page Layout

Start with a function that handles the HTML boilerplate:

```php
use Epic64\Elem\Element;
use function Epic64\Elem\{html, head, title, meta, body};

/**
 * @param string $pageTitle
 * @param list<Element> $headSlot Additional elements for <head>
 * @param list<Element> $bodySlot Main body content
 */
function pageLayout(
    string $pageTitle,
    array $headSlot = [],
    array $bodySlot = [],
): Element {
    return html(lang: 'en')(
        head()(
            meta(charset: 'UTF-8'),
            meta(name: 'viewport', content: 'width=device-width, initial-scale=1.0'),
            title(text: $pageTitle),
            ...$headSlot,
        ),
        body()(
            ...$bodySlot,
        )
    );
}
```

#### Multi-Slot Dashboard Layout

Build on the base layout to create more complex templates:

```php
use function Epic64\Elem\{div, el, stylesheet};

/**
 * @param string $pageTitle
 * @param list<Element> $headerSlot Header content (logo, nav, user menu)
 * @param list<Element> $sidebarSlot Sidebar navigation
 * @param list<Element> $mainSlot Main content area
 * @param list<Element> $footerSlot Footer content
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
        headSlot: [
            stylesheet('/css/dashboard.css'),
        ],
        bodySlot: [
            div(class: 'dashboard-layout')(
                el('header', class: 'dashboard-header')(...$headerSlot),
                el('aside', class: 'dashboard-sidebar')(...$sidebarSlot),
                el('main', class: 'dashboard-main')(...$mainSlot),
                el('footer', class: 'dashboard-footer')(...$footerSlot),
            ),
        ],
    );
}
```

#### Reusable Component Layouts

The same pattern works for smaller components like cards and modals:

```php
/**
 * @param string $cardTitle
 * @param list<Element> $headerSlot Badges, action buttons
 * @param list<Element> $bodySlot Main card content
 * @param list<Element> $footerSlot Footer actions
 */
function cardLayout(
    string $cardTitle,
    array $headerSlot = [],
    array $bodySlot = [],
    array $footerSlot = [],
): Element {
    return div(class: 'card')(
        div(class: 'card-header')(
            h(3, class: 'card-title', text: $cardTitle),
            count($headerSlot) > 0 ? div(class: 'card-actions')(...$headerSlot) : null,
        ),
        div(class: 'card-body')(...$bodySlot),
        count($footerSlot) > 0 ? div(class: 'card-footer')(...$footerSlot) : null,
    );
}
```

#### Using Layouts

Fill only the slots you need - empty slots render nothing:

```php
return dashboardLayout(
    pageTitle: 'My Dashboard',

    headerSlot: [
        h(1, text: '🚀 My App'),
        badge('Admin', BadgeVariant::Primary),
    ],

    sidebarSlot: [
        navMenu($menuItems),
    ],

    mainSlot: [
        // Nest card layouts inside the dashboard
        cardLayout(
            cardTitle: 'User Statistics',
            headerSlot: [badge('Live', BadgeVariant::Success)],
            bodySlot: [
                statCard(new Stat('Total Users', 1234, Trend::Up)),
                statCard(new Stat('Active Today', 892, Trend::Up)),
            ],
            footerSlot: [
                a('/users', text: 'View all users →'),
            ],
        ),

        cardLayout(
            cardTitle: 'Recent Activity',
            bodySlot: [activityFeed($recentActivity)],
        ),
    ],

    footerSlot: [
        p(text: '© 2024 My App'),
    ],
);
```

**Benefits over traditional templating:**

- **Type-safe slots**: PHPDoc `@param list<Element>` ensures you pass valid content
- **No inheritance complexity**: Just function composition
- **Flexible nesting**: Layouts can contain other layouts
- **Conditional slots**: Use `count($slot) > 0` to skip empty wrappers
- **IDE support**: Full autocomplete and refactoring

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
public function __invoke(DOMNode|Element|RawHtml|string|iterable|null ...$children): static
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
