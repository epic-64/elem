<?php

declare(strict_types=1);

namespace VibeHtml\Elements;

use VibeHtml\Element;

class Label extends Element
{
    public function __construct(?string $text = null, ?string $for = null)
    {
        parent::__construct('label', $text);
        if ($for !== null) {
            $this->element->setAttribute('for', $for);
        }
    }

    public function for(string $for): static
    {
        return $this->attr('for', $for);
    }
}
