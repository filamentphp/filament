<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanBeToggled
{
    protected bool | Closure $isToggleable = false;

    public function toggleable(bool | Closure $condition = true): static
    {
        $this->isToggleable = $condition;

        return $this;
    }

    public function isToggleable(): bool
    {
        return (bool) $this->evaluate($this->isToggleable);
    }

    public function isToggled(): bool
    {
        return $this->getTable()->getLivewire()->isToggled($this->getName());
    }
}
