<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanWrap
{
    protected bool | Closure $wrap = false;

    public function wrap(bool | Closure $condition = true): static
    {
        $this->wrap = $condition;

        return $this;
    }

    public function canWrap(): ?bool
    {
        return (bool) $this->evaluate($this->wrap);
    }
}
