<?php

declare(strict_types=1);

namespace Epic64\Elem\Elements;

use Epic64\Elem\Element;

class Html extends Element
{
    public function __construct(?string $lang = null)
    {
        parent::__construct('html');
        if ($lang !== null) {
            $this->element->setAttribute('lang', $lang);
        }
    }

    public function lang(string $lang): static
    {
        return $this->attr('lang', $lang);
    }

    public function toHtml(bool $pretty = false): string
    {
        $doctype = "<!DOCTYPE html>\n";
        return $doctype . parent::toHtml($pretty);
    }
}
