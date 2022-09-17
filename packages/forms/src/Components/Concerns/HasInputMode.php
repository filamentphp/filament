<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasInputMode
{
    protected string | Closure | null $inputMode = null;

    public function inputMode(string | Closure | null $mode): static
    {
        $this->inputMode = $mode;

        return $this;
    }

    public function getInputMode(): ?string
    {
        return $this->evaluate($this->inputMode);
    }
}
