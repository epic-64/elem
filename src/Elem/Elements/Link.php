<?php

declare(strict_types=1);

namespace Epic64\Elem\Elements;

use Epic64\Elem\Element;

/**
 * Represents a <link> element for linking external resources.
 *
 * Note: For stylesheets, you probably want to use the stylesheet() helper function instead,
 * which provides a more convenient API: stylesheet('/css/style.css')
 */
class Link extends Element
{
    public function __construct(?string $href = null, ?string $rel = null)
    {
        parent::__construct('link');
        if ($href !== null) {
            $this->element->setAttribute('href', $href);
        }
        if ($rel !== null) {
            $this->element->setAttribute('rel', $rel);
        }
    }

    public function href(string $href): static
    {
        return $this->attr('href', $href);
    }

    public function rel(string $rel): static
    {
        return $this->attr('rel', $rel);
    }

    public function type(string $type): static
    {
        return $this->attr('type', $type);
    }

    public function media(string $media): static
    {
        return $this->attr('media', $media);
    }

    public function sizes(string $sizes): static
    {
        return $this->attr('sizes', $sizes);
    }
}
