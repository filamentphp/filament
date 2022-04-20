<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanBeToggled
{
    protected bool | Closure $isToggleable = false;

    protected bool | Closure $isToggledByDefault = false;

    public function toggleable(bool | Closure $condition = true, bool | Closure $isToggledByDefault = false): static
    {
        $this->isToggleable = $condition;
        $this->isToggledByDefault = $isToggledByDefault;

        return $this;
    }

    public function toggledByDefault(bool | Closure $condition = true): static
    {
        $this->isToggledByDefault = $condition;

        return $this;
    }

    public function isToggledByDefault(): bool
    {
        return (bool) $this->evaluate($this->isToggledByDefault);
    }

    public function isToggleable(): bool
    {
        return (bool) $this->evaluate($this->isToggleable);
    }

    public function isToggled(): bool
    {
        return $this->getTable()->getLivewire()->isTableColumnToggled($this->getName());
    }
}
