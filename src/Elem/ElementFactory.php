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

    /**
     * Create a document fragment from raw HTML string.
     * The HTML is parsed and inserted without escaping.
     */
    public static function createRawFragment(string $html): DocumentFragment
    {
        $fragment = self::getDom()->createDocumentFragment();
        if ($html === '') {
            return $fragment;
        }

        // Parse HTML using a temporary HTMLDocument (handles real HTML5)
        $tempDoc = HTMLDocument::createFromString(
            '<div id="__raw_container__">' . $html . '</div>',
            LIBXML_NOERROR
        );

        // Find our container and import its children
        $container = $tempDoc->getElementById('__raw_container__');
        if ($container !== null) {
            foreach ($container->childNodes as $child) {
                $imported = self::getDom()->importNode($child, true);
                $fragment->appendChild($imported);
            }
        }

        return $fragment;
    }
}
