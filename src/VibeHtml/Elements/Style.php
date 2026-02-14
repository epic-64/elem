<?php

declare(strict_types=1);

namespace VibeHtml\Elements;

use VibeHtml\Element;

class Style extends Element
{
    public function __construct(?string $css = null)
    {
        parent::__construct('style', $css);
    }
}
