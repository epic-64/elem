<?php

declare(strict_types=1);

namespace Epic64\Elem\Elements;

use Epic64\Elem\Element;

class Button extends Element
{
    public function __construct(?string $text = null, string $type = 'button')
    {
        parent::__construct('button', $text);
        $this->element->setAttribute('type', $type);
    }

    public function type(string $type): static
    {
        return $this->attr('type', $type);
    }

    public function disabled(): static
    {
        return $this->attr('disabled', 'disabled');
    }
}
