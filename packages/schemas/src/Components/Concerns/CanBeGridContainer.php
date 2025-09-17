<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;

trait CanBeGridContainer
{
    protected bool | Closure $isGridContainer = false;

    public function gridContainer(bool | Closure $condition = true): static
    {
        $this->isGridContainer = $condition;

        return $this;
    }

    public function isGridContainer(): bool
    {
        return (bool) $this->evaluate($this->isGridContainer);
    }
}
