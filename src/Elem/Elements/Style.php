<?php

declare(strict_types=1);

namespace Epic64\Elem\Elements;

use Epic64\Elem\Element;

class Style extends Element
{
    public function __construct(?string $css = null)
    {
        parent::__construct('style', $css);
    }
}
