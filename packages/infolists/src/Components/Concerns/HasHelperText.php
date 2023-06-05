<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasHelperText
{
    protected string | Htmlable | Closure | null $helperText = null;

    public function helperText(string | Htmlable | Closure | null $text): static
    {
        $this->helperText = $text;

        return $this;
    }

    public function getHelperText(): string | Htmlable | null
    {
        return $this->evaluate($this->helperText);
    }
}
