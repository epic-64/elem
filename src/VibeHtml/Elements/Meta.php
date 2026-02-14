<?php

declare(strict_types=1);

namespace VibeHtml\Elements;

use VibeHtml\Element;

class Meta extends Element
{
    public function __construct(?string $charset = null, ?string $name = null, ?string $content = null)
    {
        parent::__construct('meta');
        if ($charset !== null) {
            $this->element->setAttribute('charset', $charset);
        }
        if ($name !== null) {
            $this->element->setAttribute('name', $name);
        }
        if ($content !== null) {
            $this->element->setAttribute('content', $content);
        }
    }

    public function charset(string $charset): static
    {
        return $this->attr('charset', $charset);
    }

    public function name(string $name): static
    {
        return $this->attr('name', $name);
    }

    public function content(string $content): static
    {
        return $this->attr('content', $content);
    }
}
