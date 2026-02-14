<?php

declare(strict_types=1);

namespace Epic64\Elem\Elements;

use Epic64\Elem\Element;

class Heading extends Element
{
    public function __construct(int $level, ?string $text = null)
    {
        $level = max(1, min(6, $level));
        parent::__construct("h$level", $text);
    }
}
