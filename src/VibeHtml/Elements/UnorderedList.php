<?php

declare(strict_types=1);

namespace VibeHtml\Elements;

use VibeHtml\Element;

class UnorderedList extends Element
{
    public function __construct()
    {
        parent::__construct('ul');
    }

    public function item(string|Element $content): static
    {
        $li = new ListItem();
        $li($content);
        $this($li);
        return $this;
    }
}
