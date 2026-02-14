<?php

declare(strict_types=1);

namespace VibeHtml\Elements;

use VibeHtml\Element;

class Form extends Element
{
    public function __construct(?string $action = null, string $method = 'post')
    {
        parent::__construct('form');
        if ($action !== null) {
            $this->element->setAttribute('action', $action);
        }
        $this->element->setAttribute('method', $method);
    }

    public function action(string $action): static
    {
        return $this->attr('action', $action);
    }

    public function method(string $method): static
    {
        return $this->attr('method', $method);
    }
}
