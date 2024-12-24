<?php

namespace Filament\Support\Concerns;

use Closure;

trait CanBeVertical
{
    protected bool | Closure $isVertical = false;

    protected bool $isVerticalCache;

    public function vertical(bool | Closure $condition = true): static
    {
        $this->isVertical = $condition;

        return $this;
    }

    public function isVertical(): bool
    {
        return $this->isVerticalCache ??= (bool) $this->evaluate($this->isVertical);
    }
}
