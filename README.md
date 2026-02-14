# VibeHtml

A fluent, type-safe PHP library for building HTML documents using the DOM.

## Requirements

- PHP 8.4+
- ext-dom

## Installation

```bash
composer require warp/vibe-html
```

## Usage

### Basic Elements

```php
use function VibeHtml\div;
use function VibeHtml\p;
use function VibeHtml\a;
use function VibeHtml\span;

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
use function VibeHtml\html;
use function VibeHtml\head;
use function VibeHtml\body;
use function VibeHtml\title;
use function VibeHtml\meta;
use function VibeHtml\div;
use function VibeHtml\h;
use function VibeHtml\p;

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
use function VibeHtml\form;
use function VibeHtml\label;
use function VibeHtml\input;
use function VibeHtml\button;

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
use function VibeHtml\ul;
use function VibeHtml\li;

// Using item() helper
$list = ul(class: 'nav')
    ->item('Home')
    ->item('About')
    ->item('Contact');

// Or using children
$list = ul(class: 'nav')(
    li(text: 'Home'),
    li(text: 'About'),
    li(text: 'Contact')
);
```

### Tables

```php
use function VibeHtml\table;
use function VibeHtml\tr;
use function VibeHtml\th;
use function VibeHtml\td;

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
use function VibeHtml\form;

$form = form(id: 'my-form', action: '/submit')->script(<<<JS
    el.addEventListener('submit', (e) => {
        e.preventDefault();
        console.log('Form submitted!');
    });
JS);
```

### Using Array Results (e.g., from array_map)

```php
use function VibeHtml\ul;
use function VibeHtml\li;

$items = ['Apple', 'Banana', 'Cherry'];

$list = ul()(
    array_map(fn($item) => li(text: $item), $items)
);
```

## Element Classes

All element classes extend the base `Element` class and provide fluent interfaces:

- **Structure**: `Html`, `Head`, `Body`, `Title`, `Meta`, `Style`, `Script`
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

## Scoped DOM Documents

For advanced use cases (like testing or comparing documents), you can use isolated scopes:

```php
use VibeHtml\ElementFactory;

// Execute code in an isolated scope
$html = ElementFactory::withScope(function() {
    return div(text: 'Isolated');
});
```

## License

MIT
