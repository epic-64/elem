<?php

declare(strict_types=1);

namespace VibeHtml\Elements;

use VibeHtml\Element;

class Image extends Element
{
    public function __construct(string $src, string $alt = '')
    {
        parent::__construct('img');
        $this->element->setAttribute('src', $src);
        $this->element->setAttribute('alt', $alt);
    }

    public function src(string $src): static
    {
        return $this->attr('src', $src);
    }

    public function alt(string $alt): static
    {
        return $this->attr('alt', $alt);
    }

    public function width(int $width): static
    {
        return $this->attr('width', (string)$width);
    }

    public function height(int $height): static
    {
        return $this->attr('height', (string)$height);
    }
}
