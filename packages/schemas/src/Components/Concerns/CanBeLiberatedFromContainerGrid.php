<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;

trait CanBeLiberatedFromContainerGrid
{
    protected bool | Closure $isLiberatedFromContainerGrid = false;

    public function liberatedFromContainerGrid(bool | Closure $condition = true): static
    {
        $this->isLiberatedFromContainerGrid = $condition;

        return $this;
    }

    public function isLiberatedFromContainerGrid(): bool
    {
        return (bool) $this->evaluate($this->isLiberatedFromContainerGrid);
    }
}
