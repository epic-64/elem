<?php

declare(strict_types=1);

namespace VibeHtml\Elements;

use VibeHtml\Element;

class Table extends Element
{
    public function __construct()
    {
        parent::__construct('table');
    }
}
