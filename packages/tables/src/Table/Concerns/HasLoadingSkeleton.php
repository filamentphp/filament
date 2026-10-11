<?php

namespace Filament\Tables\Table\Concerns;

use Closure;

trait HasLoadingSkeleton
{
    protected bool | Closure $hasLoadingSkeleton = false;

    public function loadingSkeleton(bool | Closure $condition = true): static
    {
        $this->hasLoadingSkeleton = $condition;

        return $this;
    }

    public function hasLoadingSkeleton(): bool
    {
        return (bool) $this->evaluate($this->hasLoadingSkeleton);
    }
}
