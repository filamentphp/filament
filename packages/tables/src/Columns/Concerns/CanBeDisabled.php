<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Filament\Tables\Columns\Contracts\Editable;

trait CanBeDisabled
{
    protected bool | Closure $isDisabled = false;

    protected bool | Closure $isClickDisabled = false;

    public function disabled(bool | Closure $condition = true): static
    {
        $this->isDisabled = $condition;

        return $this;
    }

    public function disabledClick(bool | Closure $condition = true): static
    {
        $this->isClickDisabled = $condition;

        return $this;
    }

    /**
     * @deprecated Use `disabledClick()` instead.
     */
    public function disableClick(bool | Closure $condition = true): static
    {
        $this->disabledClick($condition);

        return $this;
    }

    public function isDisabled(): bool
    {
        if (($this instanceof Editable) && $this->hasTable() && $this->getTable()->isReadOnly()) {
            return true;
        }

        return (bool) $this->evaluate($this->isDisabled);
    }

    public function isEnabled(): bool
    {
        return ! $this->isDisabled();
    }

    public function isClickDisabled(): bool
    {
        return (bool) $this->evaluate($this->isClickDisabled);
    }
}
