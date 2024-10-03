<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanBeCompacted
{
    protected bool | Closure $isCompact = false;

    public function compact(bool | Closure $condition = true): static
    {
        $this->isCompact = $condition;

        return $this;
    }

    public function isCompact(): bool
    {
        return (bool) $this->evaluate($this->isCompact);
    }
}
