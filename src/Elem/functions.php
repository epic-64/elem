<?php

declare(strict_types=1);

namespace Epic64\Elem;

use Epic64\Elem\Elements\Anchor;
use Epic64\Elem\Elements\Body;
use Epic64\Elem\Elements\Button;
use Epic64\Elem\Elements\Div;
use Epic64\Elem\Elements\Form;
use Epic64\Elem\Elements\Head;
use Epic64\Elem\Elements\Heading;
use Epic64\Elem\Elements\Html;
use Epic64\Elem\Elements\Image;
use Epic64\Elem\Elements\Input;
use Epic64\Elem\Elements\Label;
use Epic64\Elem\Elements\ListItem;
use Epic64\Elem\Elements\Meta;
use Epic64\Elem\Elements\OrderedList;
use Epic64\Elem\Elements\Paragraph;
use Epic64\Elem\Elements\Script;
use Epic64\Elem\Elements\Select;
use Epic64\Elem\Elements\Span;
use Epic64\Elem\Elements\Style;
use Epic64\Elem\Elements\Table;
use Epic64\Elem\Elements\TableCell;
use Epic64\Elem\Elements\TableHeader;
use Epic64\Elem\Elements\TableRow;
use Epic64\Elem\Elements\Textarea;
use Epic64\Elem\Elements\Title;
use Epic64\Elem\Elements\UnorderedList;

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

function html(?string $lang = null): Html
{
    return new Html($lang);
}

function head(): Head
{
    return new Head();
}

function body(?string $id = null, ?string $class = null): Body
{
    /** @var Body */
    return _applyCommon(new Body(), $id, $class);
}

function title(?string $text = null): Title
{
    return new Title($text);
}

function meta(?string $charset = null, ?string $name = null, ?string $content = null): Meta
{
    return new Meta($charset, $name, $content);
}

function style(?string $css = null): Style
{
    return new Style($css);
}

function script(?string $code = null, ?string $src = null): Script
{
    return new Script($code, $src);
}

function stylesheet(string $href): Element
{
    return new Element('link')->attr('rel', 'stylesheet')->attr('href', $href);
}

