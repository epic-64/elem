<?php

declare(strict_types=1);

namespace Epic64\Elem;

/**
 * Represents raw HTML content that should not be escaped.
 *
 * WARNING: Only use this with trusted content. Using raw HTML with
 * user-provided input can lead to XSS vulnerabilities.
 *
 * @example
 * ```php
 * div()(
 *     raw('<strong>Bold text</strong>'),
 *     raw($trustedHtmlFromDatabase)
 * )
 * ```
 */
class RawHtml
{
    public function __construct(
        public readonly string $html
    ) {}

    public function __toString(): string
    {
        return $this->html;
    }
}
