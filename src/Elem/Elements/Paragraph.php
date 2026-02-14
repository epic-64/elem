<?php

declare(strict_types=1);

namespace Epic64\Elem\Elements;

use Epic64\Elem\Element;

class Paragraph extends Element
{
    public function __construct(?string $text = null)
    {
        parent::__construct('p', $text);
    }
}
