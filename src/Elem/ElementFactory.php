<?php

declare(strict_types=1);

namespace Epic64\Elem;

use DOMDocument;
use DOMDocumentFragment;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * Factory for creating DOM elements.
 * Uses a shared DOMDocument for efficient element creation.
 */
class ElementFactory
{
    private static ?DOMDocument $dom = null;

    private static function getDom(): DOMDocument
    {
        if (self::$dom === null) {
            self::$dom = new DOMDocument('1.0', 'UTF-8');
        }
        return self::$dom;
    }

    public static function createElement(string $tagName, ?string $text = null): DOMElement
    {
        $element = self::getDom()->createElement($tagName);
        if ($text !== null && $text !== '') {
            $element->appendChild(self::createTextNode($text));
        }
        return $element;
    }

    public static function createTextNode(string $text): DOMText
    {
        return self::getDom()->createTextNode($text);
    }

    public static function importNode(DOMNode $node, bool $deep = true): DOMNode
    {
        return self::getDom()->importNode($node, $deep);
    }

    public static function saveHTML(DOMNode $node): string
    {
        return self::getDom()->saveHTML($node) ?: '';
    }

    public static function dom(): DOMDocument
    {
        return self::getDom();
    }

    /**
     * Create a document fragment from raw HTML string.
     * The HTML is parsed and inserted without escaping.
     */
    public static function createRawFragment(string $html): DOMDocumentFragment
    {
        $fragment = self::getDom()->createDocumentFragment();
        if ($html !== '') {
            // Suppress warnings for potentially malformed HTML
            @$fragment->appendXML($html);
        }
        return $fragment;
    }
}
