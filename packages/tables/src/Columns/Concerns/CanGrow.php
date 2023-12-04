<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanGrow
{
    protected bool | Closure | null $canGrow = null;

    public function grow(bool | Closure | null $condition = true): static
    {
        $this->canGrow = $condition;

        return $this;
    }

    public function canGrow(bool $default = false): bool
    {
        return (bool) ($this->evaluate($this->canGrow) ?? $default);
    }
}
