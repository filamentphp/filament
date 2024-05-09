<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanBeToggled
{
    protected bool | Closure $isToggleable = false;

    protected bool | Closure $isToggledHiddenByDefault = false;

    public function toggleable(bool | Closure $condition = true, bool | Closure $isToggledHiddenByDefault = false): static
    {
        $this->isToggleable = $condition;
        $this->toggledHiddenByDefault($isToggledHiddenByDefault);

        return $this;
    }

    public function toggledHiddenByDefault(bool | Closure $condition = true): static
    {
        $this->isToggledHiddenByDefault = $condition;

        return $this;
    }

    public function isToggledHiddenByDefault(): bool
    {
        return (bool) $this->evaluate($this->isToggledHiddenByDefault);
    }

    public function isToggleable(): bool
    {
        return (bool) $this->evaluate($this->isToggleable);
    }

    public function isToggledHidden(): bool
    {
        if (! $this->isToggleable()) {
            return false;
        }

        return $this->getTable()->getLivewire()->isTableColumnToggledHidden($this->getName());
    }
}
