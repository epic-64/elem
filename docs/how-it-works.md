# How It Works

Elem is built on PHP's native [DOM extension](https://www.php.net/manual/en/book.dom.php), giving you rock-solid HTML generation with proper escaping and well-formed output.

## The Class Model

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

## The `__invoke` Magic

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

## Why the DOM?

Using PHP's DOM extension instead of string concatenation provides several benefits:

1. **Automatic escaping** - Text content is automatically escaped, preventing XSS vulnerabilities
2. **Well-formed output** - The DOM ensures valid HTML structure
3. **Familiar model** - If you know the browser DOM, you know how Elem works internally
4. **Extensible** - You can access the underlying `DOMElement` if needed for advanced use cases

## Element Lifecycle

1. **Creation** - `div()` creates a new `Div` instance with a `DOMElement`
2. **Configuration** - Fluent methods like `->id()`, `->class()`, `->attr()` modify the `DOMElement`
3. **Children** - Calling the element as a function (`()`) appends children to the `DOMElement`
4. **Rendering** - `__toString()` or `->toHtml()` serializes the `DOMElement` to HTML

```php
// 1. Creation
$card = div(class: 'card');

// 2. Configuration  
$card->id('my-card')->data('theme', 'dark');

// 3. Children
$card(
    h(2, text: 'Title'),
    p(text: 'Content')
);

// 4. Rendering
echo $card;
```

## Handling Different Child Types

The `__invoke` method is flexible about what it accepts:

```php
div()(
    // Elements
    p(text: 'A paragraph'),
    
    // Raw strings (escaped automatically)
    'Plain text',
    
    // Arrays of elements
    ...array_map(fn($i) => li(text: $i), $items),
    
    // Iterables (generators, collections)
    $laravelCollection->map(fn($u) => userCard($u)),
    
    // Null (ignored, useful for conditionals)
    $showBadge ? badge('New') : null,
    
    // RawHtml (unescaped, use with caution)
    raw('<svg>...</svg>')
);
```

## Pretty Printing

By default, `__toString()` outputs formatted HTML with indentation. You can control this:

```php
// Pretty-printed (default for __toString)
echo $element;
echo $element->toPrettyHtml();

// Minified
echo $element->toHtml(pretty: false);
```
