<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;
use Filament\Tables\Enums\FiltersLayout;

trait HasPlacement
{
    protected FiltersLayout | Closure | null $placement = null;

    public function placement(FiltersLayout | Closure | null $placement): static
    {
        $this->placement = $placement;

        return $this;
    }

    public function getPlacement(): FiltersLayout
    {
        return $this->evaluate($this->placement) ?? $this->getTable()->getFiltersLayout();
    }
}
