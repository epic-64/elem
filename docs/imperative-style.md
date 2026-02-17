# Imperative Style

While Elem encourages a functional, declarative style, sometimes imperative code is clearer—especially for conditionals and loops. The `tap()` method lets you drop into imperative mode without breaking your fluent chain.

## The `tap()` Method

`tap()` passes the element to a callback, letting you modify it imperatively, then returns the element for continued chaining:

```php
use function Epic64\Elem\div;

div(class: 'user-card')
    ->tap(function ($el) use ($isAdmin) {
        if ($isAdmin) {
            $el->class('admin');
            $el->data('role', 'administrator');
        }
    })
    ->attr('title', 'User Profile')
```

## Conditionals

### The `when()` Method

For simple conditionals, `when()` is cleaner than `tap()` with an if statement:

```php
use function Epic64\Elem\div;

div(class: 'card')
    ->when($isAdmin, fn($el) => $el->class('admin')->data('role', 'administrator'))
    ->when($isActive, fn($el) => $el->class('active'))
    ->when($isPremium, fn($el) => $el->class('premium'))
```

The callback only runs if the condition is `true`. The element is always returned for chaining.

### Functional vs Imperative

**Functional approach** - works well for simple conditions:

```php
div(
    class: 'card ' . ($isActive ? 'active' : ''),
    ...($isAdmin ? ['data-admin' => 'true'] : [])
)
```

**Imperative approach with `tap()`** - clearer for complex conditions:

```php
div(class: 'card')->tap(function ($el) use ($isActive, $isAdmin, $permissions) {
    if ($isActive) {
        $el->class('active');
    }
    
    if ($isAdmin) {
        $el->class('admin');
        $el->data('role', 'administrator');
        
        foreach ($permissions as $permission) {
            $el->data("can-$permission", 'true');
        }
    }
})
```

## Loops

### Building Lists: Functional vs Imperative

**Functional approach** using `array_map`:

```php
use function Epic64\Elem\ul;
use function Epic64\Elem\li;

$permissions = ['read', 'write', 'delete'];

ul()(
    array_map(fn($perm) => li(class: 'permission', text: $perm), $permissions)
)
```

**Imperative approach** using `tap()` and `append()`:

```php
use Epic64\Elem\Elements\UnorderedList;
use function Epic64\Elem\ul;
use function Epic64\Elem\li;

$permissions = ['read', 'write', 'delete'];

ul()->tap(function (UnorderedList $el) use ($permissions) {
    foreach ($permissions as $perm) {
        $el->append(li(class: 'permission', text: $perm));
    }
})
```

Both produce identical output:

```html
<ul>
    <li class="permission">read</li>
    <li class="permission">write</li>
    <li class="permission">delete</li>
</ul>
```

## Closures as Children

You can also pass closures directly to the element invocation. The closure receives the element and can return children:

```php
use Epic64\Elem\Elements\Div;
use function Epic64\Elem\div;
use function Epic64\Elem\span;

div(class: 'container')(
    function (Div $el) use ($items) {
        foreach ($items as $item) {
            $el->append(span(text: $item));
        }
    }
)
```

Or return children from the closure:

```php
div(class: 'container')(
    function () use ($showExtra) {
        if ($showExtra) {
            return span(text: 'Extra content');
        }
        return null;
    }
)
```

## Combining Both Styles

Use `tap()` for modifying attributes imperatively, then continue with functional children:

```php
use function Epic64\Elem\div;
use function Epic64\Elem\ul;
use function Epic64\Elem\li;

div(class: 'user-card')
    ->tap(function ($el) use ($isAdmin) {
        if ($isAdmin) {
            $el->class('admin');
            $el->data('role', 'administrator');
        }
    })(
        ul()(
            array_map(fn($perm) => li(text: $perm), $permissions)
        )
    )
```

## When to Use Each Style

| Scenario | Recommended Style |
|----------|-------------------|
| Simple transformations | Functional (`array_map`, `list_of`) |
| Simple conditionals | `when()` |
| Complex conditionals | Imperative (`tap()`) |
| Multiple attribute modifications | Imperative (`tap()`) |
| Building lists from arrays | Functional (`array_map`) |
| Conditional attribute groups | `when()` or `tap()` |
| Mixing both | Start functional, `when()`/`tap()` when needed |

## The `append()` Method

The `append()` method adds children to an element, useful inside `tap()` callbacks:

```php
$container = div(class: 'container');

$container->append(
    p(text: 'First paragraph'),
    p(text: 'Second paragraph')
);

// Or in tap()
div(class: 'container')->tap(function ($el) use ($items) {
    foreach ($items as $item) {
        $el->append(span(text: $item));
    }
})
```

Note: `append()` is an alias for `__invoke()`, so `$el->append($child)` is equivalent to `$el($child)`.
