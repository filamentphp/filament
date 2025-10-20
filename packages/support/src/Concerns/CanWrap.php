<?php

namespace Filament\Support\Concerns;

use Closure;

trait CanWrap
{
    protected bool | Closure | null $canWrap = null;

    public function wrap(bool | Closure | null $condition = true): static
    {
        $this->canWrap = $condition;

        return $this;
    }

    public function canWrap(): bool
    {
        return (bool) ($this->evaluate($this->canWrap) ?? $this->canWrapByDefault());
    }

    public function canWrapByDefault(): bool
    {
        return false;
    }
}
