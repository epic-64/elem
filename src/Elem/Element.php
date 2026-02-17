<?php

declare(strict_types=1);

namespace Epic64\Elem;

use Closure;
use DOMElement;
use DOMNode;
use Epic64\Elem\Elements\RawHtml;
use Epic64\Elem\Elements\Text;
use InvalidArgumentException;

/**
 * Base class for all HTML elements.
 * Provides a safe, type-checked wrapper around DOMElement.
 * Uses a shared DOMDocument for efficient memory usage.
 *
 * @phpstan-type Child DOMNode|Element|RawHtml|Text|string|null
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
     * Add children to the element.
     *
     * @param Child|iterable<Child>|Closure(static): (Child|iterable<Child>) ...$children
     * @return $this
     */
    public function __invoke(mixed ...$children): static
    {
        $this->append(...$children);
        return $this;
    }

    /**
     * Add children to the element.
     *
     * @param Child|iterable<Child>|Closure(static): (Child|iterable<Child>) ...$children
     * @return $this
     */
    public function append(mixed ...$children): static
    {
        $dom = ElementFactory::dom();
        foreach ($children as $child) {
            // Skip null values (allows ternary expressions like: $condition ? element() : null)
            if ($child === null) {
                continue;
            }
            // Handle Closure - invoke it and process the result
            if ($child instanceof Closure) {
                $result = $child($this);
                if ($result !== null) {
                    $this($result);
                }
                continue;
            }
            // Handle Element instances first
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
            } elseif ($child instanceof RawHtml) {
                // Raw HTML is inserted without escaping
                if ($child->html !== '') {
                    $fragment = ElementFactory::createRawFragment($child->html);
                    $this->element->appendChild($fragment);
                }
            } elseif ($child instanceof Text) {
                // Text is escaped and inserted as a text node
                if ($child->content !== '') {
                    $this->element->appendChild(ElementFactory::createTextNode($child->content));
                }
            } elseif ($child instanceof DOMNode) {
                // External node might need import
                if ($child->ownerDocument !== $dom) {
                    $child = ElementFactory::importNode($child, true);
                }
                $this->element->appendChild($child);
            } elseif (is_iterable($child)) {
                // Handle iterables (arrays, Collections, Laravel collections, generators, etc.)
                $this(...$child);
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
            } elseif (!preg_match('/^</', $token)) {
                // Inside preformatted: skip empty text tokens but preserve non-empty ones as-is
                if (trim($token) === '') {
                    continue;
                }
            }

            // Check if it's a tag
            if (preg_match('/^<\/(\w+)/', $token, $matches)) {
                // Closing tag
                $tagName = strtolower($matches[1]);
                $wasInsidePreformatted = $insidePreformatted > 0;

                if (in_array($tagName, $preserveWhitespace)) {
                    $insidePreformatted = max(0, $insidePreformatted - 1);
                }

                $indent = max(0, $indent - 1);

                if ($wasInsidePreformatted) {
                    // Don't add indentation for closing tags when exiting preformatted content
                    $result .= $token;
                    if ($insidePreformatted === 0) {
                        $result .= "\n";
                    }
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

    /**
     * Tap into the element for imperative modifications.
     *
     * The callback receives the element and can perform any operations on it.
     * Returns the element for continued chaining.
     *
     * Usage:
     *   div(class: 'card')->tap(function($el) {
     *       if ($someCondition) {
     *           $el->class('highlighted');
     *       }
     *       $el->data('loaded', 'true');
     *   })
     *
     * @param callable(static): mixed $callback
     * @return $this
     */
    public function tap(callable $callback): static
    {
        $callback($this);
        return $this;
    }

    /**
     * Conditionally tap into the element.
     *
     * Only executes the callback if the condition is true.
     * Returns the element for continued chaining regardless.
     *
     * Usage:
     *   div(class: 'card')
     *       ->when($isAdmin, fn($el) => $el->class('admin'))
     *       ->when($isActive, fn($el) => $el->class('active'))
     *
     * @param callable(static): mixed $callback
     * @return $this
     */
    public function when(bool $condition, callable $callback): static
    {
        if ($condition) {
            $callback($this);
        }
        return $this;
    }
}
