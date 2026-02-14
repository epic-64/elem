<?php

/**
 * Base class for all HTML elements.
 * Provides a safe, type-checked wrapper around DOMDocument/DOMElement.
 */
class Element
{
    protected DOMDocument $dom;
    protected DOMElement $element;

    public function __construct(string $tagName, ?string $text = null)
    {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->element = $this->dom->createElement($tagName, $text ?? '');
        $this->dom->appendChild($this->element);
    }

    /**
     * Add children to the element. Accepts DOMNode|string|Element.
     * @param DOMNode|Element|string ...$children
     * @return $this
     */
    public function __invoke(DOMNode|Element|string ...$children): static
    {
        foreach ($children as $child) {
            if ($child instanceof DOMNode) {
                $this->element->appendChild($this->dom->importNode($child, true));
            } elseif ($child instanceof Element) {
                $this->element->appendChild($this->dom->importNode($child->getElement(), true));
            } elseif (is_string($child)) {
                $this->element->appendChild($this->dom->createTextNode($child));
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

    public function getElement(): DOMElement
    {
        return $this->element;
    }

    public function getDom(): DOMDocument
    {
        return $this->dom;
    }

    public function toHtml(): string
    {
        return $this->dom->saveHTML($this->element) ?: '';
    }

    public function __toString(): string
    {
        return $this->toHtml();
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

function el(string $tag, ?string $text = null, ?string $id = null, ?string $class = null): Element
{
    return _applyCommon(new Element($tag, $text), $id, $class);
}

function a(string $href, ?string $text = null, ?string $id = null, ?string $class = null): Anchor
{
    /** @var Anchor */
    return _applyCommon(new Anchor($href, $text), $id, $class);
}

function div(?string $text = null, ?string $id = null, ?string $class = null): Div
{
    /** @var Div */
    return _applyCommon(new Div($text), $id, $class);
}

function span(?string $text = null, ?string $id = null, ?string $class = null): Span
{
    /** @var Span */
    return _applyCommon(new Span($text), $id, $class);
}

function p(?string $text = null, ?string $id = null, ?string $class = null): Paragraph
{
    /** @var Paragraph */
    return _applyCommon(new Paragraph($text), $id, $class);
}

function h(int $level, ?string $text = null, ?string $id = null, ?string $class = null): Heading
{
    /** @var Heading */
    return _applyCommon(new Heading($level, $text), $id, $class);
}

function img(string $src, string $alt = '', ?string $id = null, ?string $class = null): Image
{
    /** @var Image */
    return _applyCommon(new Image($src, $alt), $id, $class);
}

function button(?string $text = null, string $type = 'button', ?string $id = null, ?string $class = null): Button
{
    /** @var Button */
    return _applyCommon(new Button($text, $type), $id, $class);
}

function input(string $type = 'text', ?string $name = null, ?string $id = null, ?string $class = null): Input
{
    /** @var Input */
    return _applyCommon(new Input($type, $name), $id, $class);
}

function form(?string $action = null, string $method = 'post', ?string $id = null, ?string $class = null): Form
{
    /** @var Form */
    return _applyCommon(new Form($action, $method), $id, $class);
}

function label(?string $text = null, ?string $for = null, ?string $id = null, ?string $class = null): Label
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

function li(?string $text = null, ?string $id = null, ?string $class = null): ListItem
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

function td(?string $text = null, ?string $id = null, ?string $class = null): TableCell
{
    /** @var TableCell */
    return _applyCommon(new TableCell($text), $id, $class);
}

function th(?string $text = null, ?string $id = null, ?string $class = null): TableHeader
{
    /** @var TableHeader */
    return _applyCommon(new TableHeader($text), $id, $class);
}

function textarea(?string $name = null, ?string $content = null, ?string $id = null, ?string $class = null): Textarea
{
    /** @var Textarea */
    return _applyCommon(new Textarea($name, $content), $id, $class);
}

function select(?string $name = null, ?string $id = null, ?string $class = null): Select
{
    /** @var Select */
    return _applyCommon(new Select($name), $id, $class);
}

// ============================================================================
// Example Usage
// ============================================================================

// Simple anchor with class
$anchor = a('https://example.com', 'Example', class: 'link')(' - click here');
echo $anchor->toHtml() . "\n";

// Nested elements using id/class parameters
$card = div(id: 'my-card', class: 'card shadow')(
    h(2, 'Welcome!', class: 'title'),
    p('This is a paragraph with a ')(
        a('https://example.com', 'link', class: 'external')->blank()
    )(' inside.'),
    div(class: 'button-group')(
        button('Click me', class: 'btn btn-primary'),
        button('Cancel', class: 'btn btn-secondary')
    )
);

echo $card->toHtml() . "\n";

// Form example using id/class parameters
$loginForm = form('/login', id: 'login-form', class: 'form')(
    div(class: 'form-group')(
        label('Email:', 'email'),
        input('email', 'email', id: 'email', class: 'form-control')->placeholder('Enter email')->required()
    ),
    div(class: 'form-group')(
        label('Password:', 'password'),
        input('password', 'password', id: 'password', class: 'form-control')->placeholder('Enter password')->required()
    ),
    button('Login', 'submit', class: 'btn btn-primary')
);

echo $loginForm->toHtml() . "\n";

// List example with id/class
$menu = ul(id: 'main-menu', class: 'menu nav')(
    li('Home', class: 'nav-item'),
    li('About', class: 'nav-item'),
    li('Contact', class: 'nav-item')
);
echo $menu->toHtml() . "\n";

// Table example with id/class
$dataTable = table(id: 'users-table', class: 'data-table striped')(
    tr(class: 'header-row')(
        th('Name'),
        th('Age'),
        th('City')
    ),
    tr(class: 'data-row')->cell('John')->cell('25')->cell('NYC'),
    tr(class: 'data-row')->cell('Jane')->cell('30')->cell('LA')
);
echo $dataTable->toHtml() . "\n";
