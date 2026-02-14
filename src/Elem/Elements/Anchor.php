<?php

declare(strict_types=1);

namespace Elem\Elements;

use Elem\Element;

class Anchor extends Element
{
    public function __construct(string $href, ?string $text = null)
    {
        parent::__construct('a', $text);
        $this->element->setAttribute('href', $href);
    }

    public function href(string $href): static
    {
        return $this->attr('href', $href);
    }

    public function getHref(): string
    {
        return $this->getAttr('href');
    }

    public function target(string $target): static
    {
        return $this->attr('target', $target);
    }

    public function blank(): static
    {
        return $this->target('_blank')->attr('rel', 'noopener noreferrer');
    }
}
