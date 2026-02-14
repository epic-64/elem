<?php

declare(strict_types=1);

namespace VibeHtml\Elements;

use VibeHtml\Element;

class ListItem extends Element
{
    public function __construct(?string $text = null)
    {
        parent::__construct('li', $text);
    }
}
