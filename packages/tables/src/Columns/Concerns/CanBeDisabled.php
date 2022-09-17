<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanBeDisabled
{
    protected bool | Closure $isDisabled = false;

    protected bool | Closure $isClickDisabled = false;

    public function disabled(bool | Closure $condition = true): static
    {
        $this->isDisabled = $condition;

        return $this;
    }

    public function disableClick(bool | Closure $condition = true): static
    {
        $this->isClickDisabled = $condition;

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->evaluate($this->isDisabled);
    }

    public function isEnabled(): bool
    {
        return ! $this->isDisabled();
    }

    public function isClickDisabled(): bool
    {
        return $this->evaluate($this->isClickDisabled);
    }
}
