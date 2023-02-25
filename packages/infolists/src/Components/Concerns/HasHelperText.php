<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;
use Illuminate\Support\HtmlString;

trait HasHelperText
{
    protected string | HtmlString | Closure | null $helperText = null;

    public function helperText(string | HtmlString | Closure | null $text): static
    {
        $this->helperText = $text;

        return $this;
    }

    public function getHelperText(): string | HtmlString | null
    {
        return $this->evaluate($this->helperText);
    }
}
