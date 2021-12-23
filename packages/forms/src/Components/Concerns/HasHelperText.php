<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasHelperText
{
    protected string | Closure | null $helperText = null;

    public function helperText(string | Closure | null $text): static
    {
        $this->helperText = $text;

        return $this;
    }

    public function getHelperText(): ?string
    {
        return $this->evaluate($this->helperText);
    }
}
