<?php

declare(strict_types=1);

namespace VibeHtml\Elements;

use VibeHtml\Element;

class TableRow extends Element
{
    public function __construct()
    {
        parent::__construct('tr');
    }

    public function cell(string|Element $content): static
    {
        $td = new TableCell();
        $td($content);
        $this($td);
        return $this;
    }

    public function header(string|Element $content): static
    {
        $th = new TableHeader();
        $th($content);
        $this($th);
        return $this;
    }
}
