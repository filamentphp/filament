<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanBeDisabled
{
    protected bool | Closure $isClickDisabled = false;

    public function disableClick(bool | Closure $condition = true): static
    {
        $this->isClickDisabled = $condition;

        return $this;
    }

    public function isClickDisabled(): bool
    {
        return $this->evaluate($this->isClickDisabled);
    }
}
