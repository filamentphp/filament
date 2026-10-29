<?php

namespace Filament\Support\Concerns;

use Closure;

trait CanBeContained
{
    protected bool | Closure $isContained = true;

    protected bool $cachedIsContained;

    public function contained(bool | Closure $condition = true): static
    {
        $this->isContained = $condition;

        return $this;
    }

    public function isContained(): bool
    {
        return $this->cachedIsContained ??= (bool) $this->evaluate($this->isContained);
    }
}
