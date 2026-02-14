<?php

/**
 * Represents an isolated DOM scope for building HTML documents.
 * Each HtmlDocument has its own DOMDocument, allowing multiple
 * independent HTML structures to be built and compared.
 */
class HtmlDocument
{
    public readonly DOMDocument $dom;

    public function __construct()
    {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
    }

    public function createElement(string $tagName, ?string $text = null): DOMElement
    {
        return $this->dom->createElement($tagName, $text ?? '');
    }

    public function createTextNode(string $text): DOMText
    {
        return $this->dom->createTextNode($text);
    }

    public function importNode(DOMNode $node, bool $deep = true): DOMNode
    {
        return $this->dom->importNode($node, $deep);
    }

    public function saveHTML(DOMNode $node): string
    {
        return $this->dom->saveHTML($node) ?: '';
    }
}

/**
 * Manages DOM document scopes for element creation.
 * Provides a shared default scope for efficiency, with the ability
 * to create isolated scopes when needed (e.g., comparing documents).
 */
class ElementFactory
{
    private static ?HtmlDocument $sharedScope = null;
    private static ?HtmlDocument $currentScope = null;

    /**
     * Get the current active scope (either explicit or shared default).
     */
    public static function getScope(): HtmlDocument
    {
        if (self::$currentScope !== null) {
            return self::$currentScope;
        }

        if (self::$sharedScope === null) {
            self::$sharedScope = new HtmlDocument();
        }
        return self::$sharedScope;
    }

    /**
     * Create a new isolated scope and set it as active.
     * Returns the new scope for use in a scoped context.
     */
    public static function createScope(): HtmlDocument
    {
        $scope = new HtmlDocument();
        self::$currentScope = $scope;
        return $scope;
    }

    /**
     * Set the active scope (or null to use shared default).
     */
    public static function setScope(?HtmlDocument $scope): void
    {
        self::$currentScope = $scope;
    }

    /**
     * Execute a callback within an isolated scope.
     * Automatically restores the previous scope afterward.
     *
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    public static function withScope(callable $callback): mixed
    {
        $previousScope = self::$currentScope;
        self::$currentScope = new HtmlDocument();
        try {
            return $callback();
        } finally {
            self::$currentScope = $previousScope;
        }
    }

    public static function createElement(string $tagName, ?string $text = null): DOMElement
    {
        return self::getScope()->createElement($tagName, $text);
    }

    /**
     * Reset the shared scope (useful for testing).
     */
    public static function reset(): void
    {
        self::$sharedScope = null;
        self::$currentScope = null;
    }
}

/**
 * Base class for all HTML elements.
 * Provides a safe, type-checked wrapper around DOMElement.
 * Uses a shared DOMDocument for efficient memory usage.
 */
class Element
{
    protected DOMElement $element {
        get {
            return $this->element;
        }
    }

    public function __construct(string $tagName, ?string $text = null)
    {
        $this->element = ElementFactory::createElement($tagName, $text);
    }

    /**
     * Add children to the element. Accepts DOMNode|string|Element.
     * @param DOMNode|Element|string ...$children
     * @return $this
     */
    public function __invoke(DOMNode|Element|string ...$children): static
    {
        $scope = ElementFactory::getScope();
        foreach ($children as $child) {
            if ($child instanceof Element) {
                // Check if from same document, import if needed
                if ($child->element->ownerDocument !== $scope->dom) {
                    $imported = $scope->importNode($child->element, true);
                    $this->element->appendChild($imported);
                } else {
                    $this->element->appendChild($child->element);
                }
            } elseif ($child instanceof DOMNode) {
                // External node might need import
                if ($child->ownerDocument !== $scope->dom) {
                    $child = $scope->importNode($child, true);
                }
                $this->element->appendChild($child);
            } elseif (is_string($child)) {
                $this->element->appendChild($scope->createTextNode($child));
            }
        }
        return $this;
    }

    /**
     * Set an attribute on the element.
     */
    public function attr(string $name, string $value): static
    {
        $this->element->setAttribute($name, $value);
        return $this;
    }

    /**
     * Get an attribute value.
     */
    public function getAttr(string $name): string
    {
        return $this->element->getAttribute($name);
    }

    /**
     * Set the id attribute.
     */
    public function id(string $id): static
    {
        return $this->attr('id', $id);
    }

    /**
     * Add one or more CSS classes.
     */
    public function class(string ...$classes): static
    {
        $existing = $this->element->getAttribute('class');
        $all = array_filter(array_merge(
            $existing ? explode(' ', $existing) : [],
            $classes
        ));
        $this->element->setAttribute('class', implode(' ', array_unique($all)));
        return $this;
    }

    /**
     * Set inline styles.
     */
    public function style(string $style): static
    {
        return $this->attr('style', $style);
    }

    /**
     * Set a data-* attribute.
     */
    public function data(string $name, string $value): static
    {
        return $this->attr("data-$name", $value);
    }

    public function toHtml(bool $pretty = false): string
    {
        $scope = ElementFactory::getScope();
        if ($pretty) {
            $html = $scope->saveHTML($this->element);
            return $this->indentHtml($html);
        }

        return $scope->saveHTML($this->element);
    }

    /**
     * Indent HTML string with proper formatting.
     */
    private function indentHtml(string $html): string
    {
        $html = trim($html);
        if (empty($html)) {
            return '';
        }

        // Self-closing tags
        $selfClosing = ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'];

        $result = '';
        $indent = 0;
        $indentStr = '  ';

        // Split by tags while keeping tags
        $tokens = preg_split('/(<[^>]+>)/s', $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        if ($tokens === false) {
            return $html;
        }

        foreach ($tokens as $token) {
            $token = trim($token);
            if (empty($token)) {
                continue;
            }

            // Check if it's a tag
            if (preg_match('/^<\/(\w+)/', $token, $matches)) {
                // Closing tag
                $indent = max(0, $indent - 1);
                $result .= str_repeat($indentStr, $indent) . $token . "\n";
            } elseif (preg_match('/^<(\w+)/', $token, $matches)) {
                $tagName = strtolower($matches[1]);
                $isSelfClosing = in_array($tagName, $selfClosing) || str_ends_with($token, '/>');

                $result .= str_repeat($indentStr, $indent) . $token . "\n";

                if (!$isSelfClosing) {
                    $indent++;
                }
            } else {
                // Text content
                $result .= str_repeat($indentStr, $indent) . $token . "\n";
            }
        }

        return rtrim($result);
    }

    public function __toString(): string
    {
        return $this->toPrettyHtml();
    }

    /**
     * Output pretty-printed HTML.
     */
    public function toPrettyHtml(): string
    {
        return $this->toHtml(true);
    }
}

// ============================================================================
// Specific Element Classes
// ============================================================================

class Anchor extends Element
{
    public function __construct(string $href, ?string $text = null)
    {
        parent::__construct('a', $text);
        $this->element->setAttribute('href', $href);
    }

    public function href(string $href): static
    {
        return $this->attr('href', $href);
    }

    public function getHref(): string
    {
        return $this->getAttr('href');
    }

    public function target(string $target): static
    {
        return $this->attr('target', $target);
    }

    public function blank(): static
    {
        return $this->target('_blank')->attr('rel', 'noopener noreferrer');
    }
}

class Div extends Element
{
    public function __construct(?string $text = null)
    {
        parent::__construct('div', $text);
    }
}

class Span extends Element
{
    public function __construct(?string $text = null)
    {
        parent::__construct('span', $text);
    }
}

class Paragraph extends Element
{
    public function __construct(?string $text = null)
    {
        parent::__construct('p', $text);
    }
}

class Heading extends Element
{
    public function __construct(int $level, ?string $text = null)
    {
        $level = max(1, min(6, $level));
        parent::__construct("h$level", $text);
    }
}

class Image extends Element
{
    public function __construct(string $src, string $alt = '')
    {
        parent::__construct('img');
        $this->element->setAttribute('src', $src);
        $this->element->setAttribute('alt', $alt);
    }

    public function src(string $src): static
    {
        return $this->attr('src', $src);
    }

    public function alt(string $alt): static
    {
        return $this->attr('alt', $alt);
    }

    public function width(int $width): static
    {
        return $this->attr('width', (string)$width);
    }

    public function height(int $height): static
    {
        return $this->attr('height', (string)$height);
    }
}

class Button extends Element
{
    public function __construct(?string $text = null, string $type = 'button')
    {
        parent::__construct('button', $text);
        $this->element->setAttribute('type', $type);
    }

    public function type(string $type): static
    {
        return $this->attr('type', $type);
    }

    public function disabled(): static
    {
        return $this->attr('disabled', 'disabled');
    }
}

class Input extends Element
{
    public function __construct(string $type = 'text', ?string $name = null)
    {
        parent::__construct('input');
        $this->element->setAttribute('type', $type);
        if ($name !== null) {
            $this->element->setAttribute('name', $name);
        }
    }

    public function type(string $type): static
    {
        return $this->attr('type', $type);
    }

    public function name(string $name): static
    {
        return $this->attr('name', $name);
    }

    public function value(string $value): static
    {
        return $this->attr('value', $value);
    }

    public function placeholder(string $placeholder): static
    {
        return $this->attr('placeholder', $placeholder);
    }

    public function required(): static
    {
        return $this->attr('required', 'required');
    }

    public function disabled(): static
    {
        return $this->attr('disabled', 'disabled');
    }
}

class Form extends Element
{
    public function __construct(?string $action = null, string $method = 'post')
    {
        parent::__construct('form');
        if ($action !== null) {
            $this->element->setAttribute('action', $action);
        }
        $this->element->setAttribute('method', $method);
    }

    public function action(string $action): static
    {
        return $this->attr('action', $action);
    }

    public function method(string $method): static
    {
        return $this->attr('method', $method);
    }
}

class Label extends Element
{
    public function __construct(?string $text = null, ?string $for = null)
    {
        parent::__construct('label', $text);
        if ($for !== null) {
            $this->element->setAttribute('for', $for);
        }
    }

    public function for(string $for): static
    {
        return $this->attr('for', $for);
    }
}

class UnorderedList extends Element
{
    public function __construct()
    {
        parent::__construct('ul');
    }

    public function item(string|Element $content): static
    {
        $li = new ListItem();
        if (is_string($content)) {
            $li($content);
        } else {
            $li($content);
        }
        $this($li);
        return $this;
    }
}

class OrderedList extends Element
{
    public function __construct()
    {
        parent::__construct('ol');
    }

    public function item(string|Element $content): static
    {
        $li = new ListItem();
        $li($content);
        $this($li);
        return $this;
    }
}

class ListItem extends Element
{
    public function __construct(?string $text = null)
    {
        parent::__construct('li', $text);
    }
}

class Table extends Element
{
    public function __construct()
    {
        parent::__construct('table');
    }
}

class TableRow extends Element
{
    public function __construct()
    {
        parent::__construct('tr');
    }

    public function cell(string|Element $content): static
    {
        $td = new TableCell();
        $td($content);
        $this($td);
        return $this;
    }

    public function header(string|Element $content): static
    {
        $th = new TableHeader();
        $th($content);
        $this($th);
        return $this;
    }
}

class TableCell extends Element
{
    public function __construct(?string $text = null)
    {
        parent::__construct('td', $text);
    }

    public function colspan(int $span): static
    {
        return $this->attr('colspan', (string)$span);
    }

    public function rowspan(int $span): static
    {
        return $this->attr('rowspan', (string)$span);
    }
}

class TableHeader extends Element
{
    public function __construct(?string $text = null)
    {
        parent::__construct('th', $text);
    }

    public function colspan(int $span): static
    {
        return $this->attr('colspan', (string)$span);
    }

    public function rowspan(int $span): static
    {
        return $this->attr('rowspan', (string)$span);
    }
}

class Textarea extends Element
{
    public function __construct(?string $name = null, ?string $content = null)
    {
        parent::__construct('textarea', $content);
        if ($name !== null) {
            $this->element->setAttribute('name', $name);
        }
    }

    public function name(string $name): static
    {
        return $this->attr('name', $name);
    }

    public function rows(int $rows): static
    {
        return $this->attr('rows', (string)$rows);
    }

    public function cols(int $cols): static
    {
        return $this->attr('cols', (string)$cols);
    }

    public function placeholder(string $placeholder): static
    {
        return $this->attr('placeholder', $placeholder);
    }

    public function required(): static
    {
        return $this->attr('required', 'required');
    }
}

class Select extends Element
{
    public function __construct(?string $name = null)
    {
        parent::__construct('select');
        if ($name !== null) {
            $this->element->setAttribute('name', $name);
        }
    }

    public function name(string $name): static
    {
        return $this->attr('name', $name);
    }

    public function option(string $value, string $text, bool $selected = false): static
    {
        $option = new Option($value, $text, $selected);
        $this($option);
        return $this;
    }

    public function required(): static
    {
        return $this->attr('required', 'required');
    }
}

class Option extends Element
{
    public function __construct(string $value, ?string $text = null, bool $selected = false)
    {
        parent::__construct('option', $text);
        $this->element->setAttribute('value', $value);
        if ($selected) {
            $this->element->setAttribute('selected', 'selected');
        }
    }
}

// ============================================================================
// Helper Functions for convenient element creation
// ============================================================================

/**
 * Apply common attributes (id and class) to an element.
 * @template T of Element
 * @param T $element
 * @return T
 */
function _applyCommon(Element $element, ?string $id, ?string $class): Element
{
    if ($id !== null) {
        $element->id($id);
    }
    if ($class !== null) {
        $element->class(...explode(' ', $class));
    }
    return $element;
}

function el(string $tag, ?string $id = null, ?string $class = null, ?string $text = null): Element
{
    return _applyCommon(new Element($tag, $text), $id, $class);
}

function a(string $href, ?string $id = null, ?string $class = null, ?string $text = null): Anchor
{
    /** @var Anchor */
    return _applyCommon(new Anchor($href, $text), $id, $class);
}

function div(?string $id = null, ?string $class = null, ?string $text = null): Div
{
    /** @var Div */
    return _applyCommon(new Div($text), $id, $class);
}

function span(?string $id = null, ?string $class = null, ?string $text = null): Span
{
    /** @var Span */
    return _applyCommon(new Span($text), $id, $class);
}

function p(?string $id = null, ?string $class = null, ?string $text = null): Paragraph
{
    /** @var Paragraph */
    return _applyCommon(new Paragraph($text), $id, $class);
}

function h(int $level, ?string $id = null, ?string $class = null, ?string $text = null): Heading
{
    /** @var Heading */
    return _applyCommon(new Heading($level, $text), $id, $class);
}

function img(string $src, ?string $id = null, ?string $class = null, string $alt = ''): Image
{
    /** @var Image */
    return _applyCommon(new Image($src, $alt), $id, $class);
}

function button(?string $id = null, ?string $class = null, ?string $text = null, string $type = 'button'): Button
{
    /** @var Button */
    return _applyCommon(new Button($text, $type), $id, $class);
}

function input(string $type, ?string $id = null, ?string $class = null, ?string $name = null): Input
{
    /** @var Input */
    return _applyCommon(new Input($type, $name), $id, $class);
}

function form(?string $id = null, ?string $class = null, ?string $action = null, string $method = 'post'): Form
{
    /** @var Form */
    return _applyCommon(new Form($action, $method), $id, $class);
}

function label(?string $id = null, ?string $class = null, ?string $text = null, ?string $for = null): Label
{
    /** @var Label */
    return _applyCommon(new Label($text, $for), $id, $class);
}

function ul(?string $id = null, ?string $class = null): UnorderedList
{
    /** @var UnorderedList */
    return _applyCommon(new UnorderedList(), $id, $class);
}

function ol(?string $id = null, ?string $class = null): OrderedList
{
    /** @var OrderedList */
    return _applyCommon(new OrderedList(), $id, $class);
}

function li(?string $id = null, ?string $class = null, ?string $text = null): ListItem
{
    /** @var ListItem */
    return _applyCommon(new ListItem($text), $id, $class);
}

function table(?string $id = null, ?string $class = null): Table
{
    /** @var Table */
    return _applyCommon(new Table(), $id, $class);
}

function tr(?string $id = null, ?string $class = null): TableRow
{
    /** @var TableRow */
    return _applyCommon(new TableRow(), $id, $class);
}

function td(?string $id = null, ?string $class = null, ?string $text = null): TableCell
{
    /** @var TableCell */
    return _applyCommon(new TableCell($text), $id, $class);
}

function th(?string $id = null, ?string $class = null, ?string $text = null): TableHeader
{
    /** @var TableHeader */
    return _applyCommon(new TableHeader($text), $id, $class);
}

function textarea(?string $id = null, ?string $class = null, ?string $name = null, ?string $content = null): Textarea
{
    /** @var Textarea */
    return _applyCommon(new Textarea($name, $content), $id, $class);
}

function select(?string $id = null, ?string $class = null, ?string $name = null): Select
{
    /** @var Select */
    return _applyCommon(new Select($name), $id, $class);
}

// ============================================================================
// Example Usage
// ============================================================================

// Simple anchor with class
a('https://example.com', class: 'link', text: 'Example')(' - click here');
echo a('https://example.com', class: 'link', text: 'Example')(' - click here');
exit();

// Nested elements using id/class parameters (pretty printed)
$card = div(id: 'my-card', class: 'card shadow')(
    h(2, class: 'title', text: 'Welcome!'),
    p(text: 'This is a paragraph with a ')(
        a('https://example.com', class: 'external', text: 'link')->blank()
    )(' inside.'),
    div(class: 'button-group')(
        button(class: 'btn btn-primary', text: 'Click me'),
        button(class: 'btn btn-secondary', text: 'Cancel')
    )
);

echo "=== Pretty Printed ===\n";
echo $card->toPrettyHtml() . "\n";

// Form example using id/class parameters (pretty printed)
$loginForm = form(id: 'login-form', class: 'form', action: '/login')(
    div(class: 'form-group')(
        label(text: 'Email:', for: 'email'),
        input('email', id: 'email', class: 'form-control', name: 'email')->placeholder('Enter email')->required()
    ),
    div(class: 'form-group')(
        label(text: 'Password:', for: 'password'),
        input('password', id: 'password', class: 'form-control', name: 'password')->placeholder('Enter password')->required()
    ),
    button(class: 'btn btn-primary', text: 'Login', type: 'submit')
);

echo $loginForm->toPrettyHtml() . "\n";

// List example with id/class
$menu = ul(id: 'main-menu', class: 'menu nav')(
    li(class: 'nav-item', text: 'Home'),
    li(class: 'nav-item', text: 'About'),
    li(class: 'nav-item', text: 'Contact')
);
echo $menu->toPrettyHtml() . "\n";

// Table example with id/class
$dataTable = table(id: 'users-table', class: 'data-table striped')(
    tr(class: 'header-row')(
        th(text: 'Name'),
        th(text: 'Age'),
        th(text: 'City')
    ),
    tr(class: 'data-row')->cell('John')->cell('25')->cell('NYC'),
    tr(class: 'data-row')->cell('Jane')->cell('30')->cell('LA')
);
echo $dataTable->toPrettyHtml() . "\n";
