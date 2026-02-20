<?php

declare(strict_types=1);

namespace Epic64\Elem;

use Closure;
use Dom\Element as DomElement;
use Dom\Node;
use Epic64\Elem\Elements\RawHtml;
use Epic64\Elem\Elements\Text;
use InvalidArgumentException;

/**
 * Base class for all HTML elements.
 * Provides a safe, type-checked wrapper around Dom\Element.
 * Uses a shared HTMLDocument for efficient memory usage.
 *
 * @phpstan-type Child Node|Element|RawHtml|Text|string|null
 */
class Element
{
    /** @var array<string, true> Void elements that cannot have children */
    private const array VOID_ELEMENTS = [
        'area' => true, 'base' => true, 'br' => true, 'col' => true,
        'embed' => true, 'hr' => true, 'img' => true, 'input' => true,
        'link' => true, 'meta' => true, 'param' => true, 'source' => true,
        'track' => true, 'wbr' => true,
    ];

    /** @var array<string, true> Tags that preserve whitespace */
    private const array PRESERVE_WHITESPACE = [
        'pre' => true, 'code' => true, 'textarea' => true, 'script' => true,
    ];

    protected DomElement $element;

    protected ?DomElement $pendingScript = null;

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
                // Raw HTML: insert marker that gets replaced during serialization (no parsing!)
                if ($child->html !== '') {
                    $this->element->appendChild(ElementFactory::createRawMarker($child->html));
                }
            } elseif ($child instanceof Text) {
                // Text is escaped and inserted as a text node
                if ($child->content !== '') {
                    $this->element->appendChild(ElementFactory::createTextNode($child->content));
                }
            } elseif ($child instanceof Node) {
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
        return $this->element->getAttribute($name) ?? '';
    }

    /**
     * Set the id attribute.
     */
    public function id(string $id): static
    {
        $this->element->id = $id;
        return $this;
    }

    /**
     * Add one or more CSS classes.
     */
    public function class(string ...$classes): static
    {
        $classes = array_filter($classes, fn($c) => $c !== '');
        if ($classes !== []) {
            $this->element->classList->add(...$classes);
        }


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
        if (isset(self::VOID_ELEMENTS[strtolower($this->element->tagName)])) {
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
        if ($html === '') {
            return '';
        }

        // Pre-computed indent strings for common depths (0-20)
        static $indentCache = [
            '', '    ', '        ', '            ', '                ',
            '                    ', '                        ', '                            ',
            '                                ', '                                    ',
        ];

        $output = [];
        $indent = 0;
        $insidePreformatted = 0;

        // Split by tags while keeping tags
        $tokens = preg_split('/(<[^>]+>)/', $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        if ($tokens === false) {
            return $html;
        }

        // Collapse simple elements (open tag + text + close tag) into single tokens
        $collapsed = [];
        $count = count($tokens);
        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];
            // Check for pattern: opening tag, text content, closing tag (with no nested tags)
            if (
                $i + 2 < $count
                && $token[0] === '<'
                && $token[1] !== '/'
                && ($tokens[$i + 1][0] ?? '<') !== '<'
                && ($tokens[$i + 2][0] ?? '') === '<'
                && ($tokens[$i + 2][1] ?? '') === '/'
            ) {
                // Extract tag name from opening tag
                $spacePos = strpos($token, ' ');
                $tagEnd = $spacePos !== false ? $spacePos : strlen($token) - 1;
                $tagName = strtolower(substr($token, 1, $tagEnd - 1));

                // Only collapse if not void/preformatted and closing tag matches
                if (!isset(self::VOID_ELEMENTS[$tagName]) && !isset(self::PRESERVE_WHITESPACE[$tagName])) {
                    $closeTag = $tokens[$i + 2];
                    $closeTagName = strtolower(substr($closeTag, 2, -1));
                    if ($tagName === $closeTagName) {
                        $collapsed[] = $token . trim($tokens[$i + 1]) . $closeTag;
                        $i += 2;
                        continue;
                    }
                }
            }
            $collapsed[] = $token;
        }

        foreach ($collapsed as $token) {
            // Only trim if not inside preformatted content
            if ($insidePreformatted === 0) {
                $token = trim($token);
                if ($token === '') {
                    continue;
                }
            } elseif ($token[0] !== '<') {
                // Inside preformatted: skip empty text tokens but preserve non-empty ones as-is
                if (trim($token) === '') {
                    continue;
                }
            }

            $indentStr = $indentCache[$indent] ?? str_repeat('    ', $indent);

            // Check if token starts with <
            if ($token[0] === '<') {
                // Check if it's a closing tag
                if ($token[1] === '/') {
                    // Extract tag name
                    $tagName = strtolower(substr($token, 2, strpos($token, '>') - 2));
                    $wasInsidePreformatted = $insidePreformatted > 0;

                    if (isset(self::PRESERVE_WHITESPACE[$tagName])) {
                        $insidePreformatted = max(0, $insidePreformatted - 1);
                    }

                    $indent = max(0, $indent - 1);
                    $indentStr = $indentCache[$indent] ?? str_repeat('    ', $indent);

                    if ($wasInsidePreformatted) {
                        $output[] = $token;
                        if ($insidePreformatted === 0) {
                            $output[] = "\n";
                        }
                    } else {
                        $output[] = $indentStr . $token . "\n";
                    }
                } else {
                    // Opening tag or self-closing - check if it's a collapsed element
                    if (str_contains($token, '</')) {
                        // Collapsed element like <tag>text</tag>
                        $output[] = $indentStr . $token . "\n";
                        continue;
                    }

                    // Extract tag name
                    $spacePos = strpos($token, ' ');
                    $tagEnd = $spacePos !== false ? $spacePos : strlen($token) - 1;
                    $tagName = strtolower(substr($token, 1, $tagEnd - 1));
                    $isSelfClosing = isset(self::VOID_ELEMENTS[$tagName]) || $token[-2] === '/';

                    if ($insidePreformatted > 0) {
                        $output[] = $token;
                    } else {
                        $output[] = $indentStr . $token . "\n";
                    }

                    if (!$isSelfClosing) {
                        $indent++;
                        if (isset(self::PRESERVE_WHITESPACE[$tagName])) {
                            $insidePreformatted++;
                        }
                    }
                }
            } else {
                // Text content
                if ($insidePreformatted > 0) {
                    $output[] = $token;
                } else {
                    $output[] = $indentStr . $token . "\n";
                }
            }
        }

        return rtrim(implode('', $output));
    }

    public function __toString(): string
    {
        return $this->toHtml(pretty: false); // pretty is 30-40% slower.
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
