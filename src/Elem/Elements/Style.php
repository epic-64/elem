<?php

declare(strict_types=1);

namespace Elem\Elements;

use Elem\Element;

class Style extends Element
{
    public function __construct(?string $css = null)
    {
        parent::__construct('style', $css);
    }
}
