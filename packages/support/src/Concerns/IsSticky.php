<?php

namespace Filament\Support\Concerns;

use Closure;

trait IsSticky
{
    protected bool | Closure | null $isSticky = null;

    public function sticky(bool | Closure | null $condition = true): static
    {
        $this->isSticky = $condition;

        return $this;
    }

    public function isSticky(bool $default = true): bool
    {
        return (bool) ($this->evaluate($this->isSticky) ?? $default);
    }
}
