<?php

declare(strict_types=1);

namespace Epic64\Elem;

use DOMElement;
use DOMNode;
use InvalidArgumentException;

/**
 * Base class for all HTML elements.
 * Provides a safe, type-checked wrapper around DOMElement.
 * Uses a shared DOMDocument for efficient memory usage.
 */
class Element
{
    protected DOMElement $element;

    protected ?DOMElement $pendingScript = null;

    public function __construct(string $tagName, ?string $text = null)
    {
        $this->element = ElementFactory::createElement($tagName, $text);
    }

    /**
     * Add children to the element. Accepts DOMNode|string|Element|array.
     * @param DOMNode|Element|string|array<DOMNode|Element|string|array<mixed>> ...$children
     * @return $this
     */
    public function __invoke(DOMNode|Element|string|array ...$children): static
    {
        $dom = ElementFactory::dom();
        foreach ($children as $child) {
            // Handle arrays (e.g., from array_map)
            if (is_array($child)) {
                /** @phpstan-ignore argument.type (recursive type is intentional for nested arrays from array_map) */
                $this(...$child);
                continue;
            }
            if ($child instanceof Element) {
                // Check if from same document, import if needed
                if ($child->element->ownerDocument !== $dom) {
                    $imported = ElementFactory::importNode($child->element, true);
                    $this->element->appendChild($imported);
                } else {
                    $this->element->appendChild($child->element);
                }
                // Handle pending script for void elements
                if ($child->pendingScript !== null) {
                    $this->element->appendChild($child->pendingScript);
                    $child->pendingScript = null;
                }
            } elseif ($child instanceof DOMNode) {
                // External node might need import
                if ($child->ownerDocument !== $dom) {
                    $child = ElementFactory::importNode($child, true);
                }
                $this->element->appendChild($child);
            } elseif (is_string($child)) {
                $this->element->appendChild(ElementFactory::createTextNode($child));
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

    /**
     * Add an inline script that receives this element as `el`.
     * Automatically wraps the code with getElementById lookup.
     * For void elements (input, img, etc), appends script as next sibling.
     *
     * Usage:
     *   form(id: 'my-form')->script(<<<JS
     *       el.addEventListener('submit', (e) => { ... });
     *   JS)
     */
    public function script(string $code): static
    {
        $id = $this->element->getAttribute('id');
        if (empty($id)) {
            throw new InvalidArgumentException('Element must have an id to use script()');
        }

        $wrappedCode = "{ const el = document.getElementById('$id'); $code }";
        $script = ElementFactory::createElement('script', $wrappedCode);

        // Void elements can't have children, so we store the script to be rendered after
        $voidElements = ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'];
        if (in_array(strtolower($this->element->tagName), $voidElements)) {
            // Insert after this element
            if ($this->element->parentNode) {
                $this->element->parentNode->insertBefore($script, $this->element->nextSibling);
            } else {
                // No parent yet - store for later
                $this->pendingScript = $script;
            }
        } else {
            $this->element->appendChild($script);
        }
        return $this;
    }

    public function toHtml(bool $pretty = false): string
    {
        if ($pretty) {
            $html = ElementFactory::saveHTML($this->element);
            return $this->indentHtml($html);
        }

        return ElementFactory::saveHTML($this->element);
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

        // Tags whose content should preserve whitespace (no indentation)
        $preserveWhitespace = ['pre', 'code', 'textarea', 'script'];

        $result = '';
        $indent = 0;
        $indentStr = '  ';
        $insidePreformatted = 0; // Track nesting level of preformatted tags

        // Split by tags while keeping tags
        $tokens = preg_split('/(<[^>]+>)/s', $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        if ($tokens === false) {
            return $html;
        }

        foreach ($tokens as $token) {
            // Only trim if not inside preformatted content
            if ($insidePreformatted === 0) {
                $token = trim($token);
                if (empty($token)) {
                    continue;
                }
            }

            // Check if it's a tag
            if (preg_match('/^<\/(\w+)/', $token, $matches)) {
                // Closing tag
                $tagName = strtolower($matches[1]);

                if (in_array($tagName, $preserveWhitespace)) {
                    $insidePreformatted = max(0, $insidePreformatted - 1);
                }

                $indent = max(0, $indent - 1);

                if ($insidePreformatted > 0) {
                    $result .= $token;
                } else {
                    $result .= str_repeat($indentStr, $indent) . $token . "\n";
                }
            } elseif (preg_match('/^<(\w+)/', $token, $matches)) {
                $tagName = strtolower($matches[1]);
                $isSelfClosing = in_array($tagName, $selfClosing) || str_ends_with($token, '/>');

                if ($insidePreformatted > 0) {
                    $result .= $token;
                } else {
                    $result .= str_repeat($indentStr, $indent) . $token . "\n";
                }

                if (!$isSelfClosing) {
                    $indent++;
                    if (in_array($tagName, $preserveWhitespace)) {
                        $insidePreformatted++;
                    }
                }
            } else {
                // Text content
                if ($insidePreformatted > 0) {
                    $result .= $token;
                } else {
                    $result .= str_repeat($indentStr, $indent) . $token . "\n";
                }
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
