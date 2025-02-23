<?php

namespace Filament\Support\Concerns;

use Closure;

trait CanWrap
{
    protected bool | Closure | null $wrap = null;

    public function wrap(bool | Closure | null $condition = true): static
    {
        $this->wrap = $condition;

        return $this;
    }

    public function canWrap(): bool
    {
        return (bool) ($this->evaluate($this->wrap) ?? $this->canWrapByDefault());
    }

    public function canWrapByDefault(): bool
    {
        return false;
    }
}
