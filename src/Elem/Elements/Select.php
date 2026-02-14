<?php

declare(strict_types=1);

namespace Elem\Elements;

use Elem\Element;

class Select extends Element
{
    public function __construct(?string $name = null)
    {
        parent::__construct('select');
        if ($name !== null) {
            $this->element->setAttribute('name', $name);
        }
    }

    public function name(string $name): static
    {
        return $this->attr('name', $name);
    }

    public function option(string $value, string $text, bool $selected = false): static
    {
        $option = new Option($value, $text, $selected);
        $this($option);
        return $this;
    }

    public function required(): static
    {
        return $this->attr('required', 'required');
    }
}
