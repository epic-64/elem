<?php

declare(strict_types=1);

namespace Epic64\Elem;

use Dom\DocumentFragment;
use Dom\Element;
use Dom\HTMLDocument;
use Dom\Node;
use Dom\Text;

/**
 * Factory for creating DOM elements.
 * Uses a shared HTMLDocument for efficient element creation.
 */
class ElementFactory
{
    private static ?HTMLDocument $dom = null;

    private static function getDom(): HTMLDocument
    {
        if (self::$dom === null) {
            self::$dom = HTMLDocument::createEmpty();
        }
        return self::$dom;
    }

    public static function createElement(string $tagName, ?string $text = null): Element
    {
        $element = self::getDom()->createElement($tagName);
        if ($text !== null && $text !== '') {
            $element->appendChild(self::createTextNode($text));
        }
        return $element;
    }

    public static function createTextNode(string $text): Text
    {
        return self::getDom()->createTextNode($text);
    }

    public static function importNode(Node $node, bool $deep = true): Node
    {
        return self::getDom()->importNode($node, $deep);
    }

    public static function saveHTML(Node $node): string
    {
        return self::getDom()->saveHtml($node);
    }

    public static function dom(): HTMLDocument
    {
        return self::getDom();
    }

    /** @var Element|null Reusable temp element for parsing raw HTML */
    private static ?Element $tempElement = null;

    /**
     * Create a document fragment from raw HTML string.
     * The HTML is parsed and inserted without escaping.
     */
    public static function createRawFragment(string $html): DocumentFragment
    {
        $dom = self::getDom();
        $fragment = $dom->createDocumentFragment();
        if ($html === '') {
            return $fragment;
        }

        // Fast path: if it looks like a simple text node (no < or &), just create text
        if (strpos($html, '<') === false && strpos($html, '&') === false) {
            $fragment->appendChild($dom->createTextNode($html));
            return $fragment;
        }

        // Use innerHTML on a reusable div element - much faster than creating new documents
        if (self::$tempElement === null) {
            self::$tempElement = $dom->createElement('div');
        }

        // Set innerHTML parses the HTML directly
        self::$tempElement->innerHTML = $html;

        // Move all children to fragment (moves, not copies - more efficient)
        while (self::$tempElement->firstChild !== null) {
            $fragment->appendChild(self::$tempElement->firstChild);
        }

        return $fragment;
    }
}
