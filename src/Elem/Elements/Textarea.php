<?php

declare(strict_types=1);

namespace Elem\Elements;

use Elem\Element;

class Textarea extends Element
{
    public function __construct(?string $name = null, ?string $content = null)
    {
        parent::__construct('textarea', $content);
        if ($name !== null) {
            $this->element->setAttribute('name', $name);
        }
    }

    public function name(string $name): static
    {
        return $this->attr('name', $name);
    }

    public function rows(int $rows): static
    {
        return $this->attr('rows', (string)$rows);
    }

    public function cols(int $cols): static
    {
        return $this->attr('cols', (string)$cols);
    }

    public function placeholder(string $placeholder): static
    {
        return $this->attr('placeholder', $placeholder);
    }

    public function required(): static
    {
        return $this->attr('required', 'required');
    }
}
