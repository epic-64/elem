<?php

declare(strict_types=1);

namespace VibeHtml\Elements;

use VibeHtml\Element;

class TableCell extends Element
{
    public function __construct(?string $text = null)
    {
        parent::__construct('td', $text);
    }

    public function colspan(int $span): static
    {
        return $this->attr('colspan', (string)$span);
    }

    public function rowspan(int $span): static
    {
        return $this->attr('rowspan', (string)$span);
    }
}
