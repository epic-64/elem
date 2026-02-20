<?php

declare(strict_types=1);

namespace Epic64\Elem;

use Dom\Comment;
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

    /** @var array<int|string, string> Raw HTML storage keyed by marker ID */
    private static array $rawHtmlStore = [];

    /** @var int Counter for unique marker IDs */
    private static int $rawHtmlCounter = 0;

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
        $html = self::getDom()->saveHtml($node);

        // Replace raw HTML markers with actual content
        if (self::$rawHtmlStore !== []) {
            $html = preg_replace_callback(
                '/<!--RAW:(\d+)-->/',
                static fn(array $m) => self::$rawHtmlStore[(string) $m[1]] ?? '',
                $html
            ) ?? '';
        }

        return $html;
    }

    public static function dom(): HTMLDocument
    {
        return self::getDom();
    }

    /**
     * Create a comment marker for raw HTML that will be replaced during serialization.
     * This avoids expensive DOM parsing for raw HTML content.
     */
    public static function createRawMarker(string $html): Comment
    {
        $id = (string) self::$rawHtmlCounter++;
        self::$rawHtmlStore[$id] = $html;
        return self::getDom()->createComment("RAW:$id");
    }
}
