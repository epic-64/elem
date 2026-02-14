<?php

declare(strict_types=1);

namespace Elem\Elements;

use Elem\Element;

class Title extends Element
{
    public function __construct(?string $text = null)
    {
        parent::__construct('title', $text);
    }
}
