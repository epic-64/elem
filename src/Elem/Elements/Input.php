<?php

declare(strict_types=1);

namespace Epic64\Elem\Elements;

use Epic64\Elem\Element;

class Input extends Element
{
    public function __construct(string $type = 'text', ?string $name = null)
    {
        parent::__construct('input');
        $this->element->setAttribute('type', $type);
        if ($name !== null) {
            $this->element->setAttribute('name', $name);
        }
    }

    public function type(string $type): static
    {
        return $this->attr('type', $type);
    }

    public function name(string $name): static
    {
        return $this->attr('name', $name);
    }

    public function value(string $value): static
    {
        return $this->attr('value', $value);
    }

    public function placeholder(string $placeholder): static
    {
        return $this->attr('placeholder', $placeholder);
    }

    public function required(): static
    {
        return $this->attr('required', 'required');
    }

    public function disabled(): static
    {
        return $this->attr('disabled', 'disabled');
    }
}
