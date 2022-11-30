<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanWrapHeader
{
    protected bool | Closure $isHeaderWrapped = false;

    public function wrapHeader(bool | Closure $condition = true): static
    {
        $this->isHeaderWrapped = $condition;

        return $this;
    }

    public function isHeaderWrapped(): bool
    {
        return (bool) $this->evaluate($this->isHeaderWrapped);
    }
}
