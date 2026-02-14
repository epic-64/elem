<?php

declare(strict_types=1);

namespace VibeHtml\Elements;

use VibeHtml\Element;

class OrderedList extends Element
{
    public function __construct()
    {
        parent::__construct('ol');
    }

    public function item(string|Element $content): static
    {
        $li = new ListItem();
        $li($content);
        $this($li);
        return $this;
    }
}
