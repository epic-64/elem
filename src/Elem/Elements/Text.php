<?php

declare(strict_types=1);

namespace Epic64\Elem\Elements;

/**
 * Represents a text node that will be safely escaped when rendered.
 *
 * This is useful when you want to explicitly create a text node as a sibling
 * to other elements, rather than using the `text` parameter of an element.
 *
 * @example
 * ```php
 * div()(
 *     text('Hello, '),
 *     span(class: 'name', text: $username),
 *     text('!')
 * )
 * ```
 */
class Text
{
    public function __construct(
        public readonly string $content
    ) {}

    public function __toString(): string
    {
        return htmlspecialchars($this->content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
