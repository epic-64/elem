<?php

declare(strict_types=1);

namespace Epic64\Elem\Elements;

use Epic64\Elem\Element;

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
