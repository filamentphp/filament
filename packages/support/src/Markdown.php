<?php

namespace Filament\Support;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use Stringable;

class Markdown implements Htmlable, Stringable
{
    public function __construct(
        protected string $text,
    ) {
    }

    public function toHtml()
    {
        return Str::markdown($this->text);
    }

    public function __toString(): string
    {
        return $this->toHtml();
    }
}
