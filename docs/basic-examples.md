# Basic Examples

This document covers the fundamental building blocks of Elem: forms, lists, tables, and dynamic content generation.

## Forms

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

### Form Group Helper

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

## Lists

```php
use function Epic64\Elem\ul;
use function Epic64\Elem\ol;
use function Epic64\Elem\li;

// Unordered list
$navList = ul(class: 'nav')(
    li(text: 'Home'),
    li(text: 'About'),
    li(text: 'Contact')
);

// Ordered list
$steps = ol(class: 'steps')(
    li(text: 'Install the package'),
    li(text: 'Import the functions'),
    li(text: 'Build your HTML')
);

// Nested lists
$nestedList = ul(class: 'menu')(
    li()(
        'Products',
        ul()(
            li(text: 'Electronics'),
            li(text: 'Clothing'),
            li(text: 'Books')
        )
    ),
    li()(
        'Services',
        ul()(
            li(text: 'Consulting'),
            li(text: 'Support')
        )
    )
);
```

## Tables

```php
use function Epic64\Elem\table;
use function Epic64\Elem\tr;
use function Epic64\Elem\th;
use function Epic64\Elem\td;
use function Epic64\Elem\el;

$table = table(class: 'data-table')(
    el('thead')(
        tr()(
            th(text: 'Name'),
            th(text: 'Email'),
            th(text: 'Role')
        )
    ),
    el('tbody')(
        tr()(
            td(text: 'Alice'),
            td(text: 'alice@example.org'),
            td(text: 'Admin')
        ),
        tr()(
            td(text: 'Bob'),
            td(text: 'bob@example.org'),
            td(text: 'User')
        )
    )
);
```

### Dynamic Tables

Generate table rows from data:

```php
use function Epic64\Elem\{table, tr, th, td, el};

readonly class User
{
    public function __construct(
        public string $name,
        public string $email,
        public string $role,
    ) {}
}

/** @var list<User> $users */
$users = [
    new User('Alice', 'alice@example.org', 'Admin'),
    new User('Bob', 'bob@example.org', 'User'),
    new User('Charlie', 'charlie@example.org', 'User'),
];

$table = table(class: 'data-table')(
    el('thead')(
        tr()(
            th(text: 'Name'),
            th(text: 'Email'),
            th(text: 'Role')
        )
    ),
    el('tbody')(
        ...array_map(
            fn(User $user) => tr()(
                td(text: $user->name),
                td(text: $user->email),
                td(text: $user->role)
            ),
            $users
        )
    )
);
```

## Dynamic Content

### Using array_map

The simplest way to generate elements from data:

```php
use function Epic64\Elem\ul;
use function Epic64\Elem\li;

$items = ['Apple', 'Banana', 'Cherry'];

$list = ul()(
    ...array_map(fn($item) => li(text: $item), $items)
);
```

### Using list_of()

The `list_of()` helper provides a fluent interface for filtering and mapping:

```php
use function Epic64\Elem\div;
use function Epic64\Elem\span;
use function Epic64\Elem\list_of;

readonly class Product
{
    public function __construct(
        public string $name,
        public float $price,
        public bool $inStock,
    ) {}
}

/** @var list<Product> $products */
$products = [
    new Product('Laptop', 999.99, true),
    new Product('Mouse', 29.99, true),
    new Product('Keyboard', 79.99, false),
    new Product('Monitor', 299.99, true),
];

// Show only in-stock products
div(class: 'product-list')(
    list_of($products)
        ->filter(fn(Product $p) => $p->inStock)
        ->map(fn(Product $p) => div(class: 'product')(
            span(class: 'name', text: $p->name),
            span(class: 'price', text: '$' . number_format($p->price, 2))
        ))
);
```

### Conditional Rendering

Use ternary operators or null to conditionally render elements:

```php
use function Epic64\Elem\div;
use function Epic64\Elem\span;
use function Epic64\Elem\p;

$user = getCurrentUser();
$isAdmin = $user->role === 'admin';
$hasNotifications = $user->notifications > 0;

div(class: 'header')(
    span(text: $user->name),
    
    // Show badge only if admin
    $isAdmin ? span(class: 'badge', text: 'Admin') : null,
    
    // Show notification count only if there are any
    $hasNotifications 
        ? span(class: 'notifications', text: (string) $user->notifications) 
        : null
);
```

### Spreading Arrays

Use the spread operator to insert multiple elements:

```php
use function Epic64\Elem\div;
use function Epic64\Elem\p;

$paragraphs = [
    p(text: 'First paragraph'),
    p(text: 'Second paragraph'),
    p(text: 'Third paragraph'),
];

div(class: 'content')(
    ...$paragraphs
);
```

### Working with Iterables

Elem accepts any iterable, including generators and Laravel collections:

```php
use function Epic64\Elem\ul;
use function Epic64\Elem\li;

// Generator
function generateItems(): Generator {
    yield li(text: 'Item 1');
    yield li(text: 'Item 2');
    yield li(text: 'Item 3');
}

ul()(generateItems());

// Laravel Collection (if using Laravel)
$collection = collect(['Red', 'Green', 'Blue']);

ul()(
    $collection->map(fn($color) => li(text: $color))
);
```
