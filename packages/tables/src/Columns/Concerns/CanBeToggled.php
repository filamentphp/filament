<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanBeToggled
{
    protected bool | Closure $isToggleable = false;

    protected bool $isToggledByDefault = false;

    public function toggleable(bool | Closure $condition = true, bool $isToggledByDefault = false): static
    {
        $this->isToggleable = $condition;
        $this->isToggledByDefault = $isToggledByDefault;

        return $this;
    }

    public function toggledByDefault(bool $condition = true): static
    {
        $this->isToggledByDefault = $condition;

        return $this;
    }

    public function isToggleable(): bool
    {
        return (bool) $this->evaluate($this->isToggleable);
    }
}
