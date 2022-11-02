<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanGrow
{
    protected bool | Closure $canGrow = true;

    public function grow(bool | Closure $condition = true): static
    {
        $this->canGrow = $condition;

        return $this;
    }

    public function canGrow(): bool
    {
        return (bool) $this->evaluate($this->canGrow);
    }
}
