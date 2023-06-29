<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanBeCloned
{
    protected bool | Closure $isCloneable = false;

    public function cloneable(bool | Closure $condition = true): static
    {
        $this->isCloneable = $condition;

        return $this;
    }

    public function isCloneable(): bool
    {
        if ($this->isDisabled()) {
            return false;
        }

        return (bool) $this->evaluate($this->isCloneable);
    }
}
