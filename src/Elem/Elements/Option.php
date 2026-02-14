<?php

declare(strict_types=1);

namespace Elem\Elements;

use Elem\Element;

class Option extends Element
{
    public function __construct(string $value, ?string $text = null, bool $selected = false)
    {
        parent::__construct('option', $text);
        $this->element->setAttribute('value', $value);
        if ($selected) {
            $this->element->setAttribute('selected', 'selected');
        }
    }
}
