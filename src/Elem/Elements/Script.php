<?php

declare(strict_types=1);

namespace Epic64\Elem\Elements;

use Epic64\Elem\Element;

class Script extends Element
{
    public function __construct(?string $code = null, ?string $src = null)
    {
        parent::__construct('script', $code);
        if ($src !== null) {
            $this->element->setAttribute('src', $src);
        }
    }

    public function src(string $src): static
    {
        return $this->attr('src', $src);
    }

    public function defer(): static
    {
        return $this->attr('defer', 'defer');
    }

    public function async(): static
    {
        return $this->attr('async', 'async');
    }

    public function type(string $type): static
    {
        return $this->attr('type', $type);
    }
}
