<?php

declare(strict_types=1);

namespace VibeHtml;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * Represents an isolated DOM scope for building HTML documents.
 * Each HtmlDocument has its own DOMDocument, allowing multiple
 * independent HTML structures to be built and compared.
 */
readonly class HtmlDocument
{
    public DOMDocument $dom;

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
