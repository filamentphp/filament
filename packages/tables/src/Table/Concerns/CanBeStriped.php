<?php

namespace Filament\Tables\Table\Concerns;

use Closure;

trait CanBeStriped
{
    protected bool | Closure $isStriped = false;

    public function striped(bool | Closure $condition = true): static
    {
        $this->isStriped = $condition;

        return $this;
    }

    public function isStriped(): bool
    {
        return (bool) $this->evaluate($this->isStriped);
    }
}
