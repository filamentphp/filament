<?php

namespace Filament\Tables\Components;

use Closure;
use Filament\Tables\Enums\TableFiltersPosition;

class TableFilters extends TablePart
{
    protected bool | Closure $isCollapsible = false;

    protected TableFiltersPosition $position = TableFiltersPosition::Auto;

    public function collapsible(bool | Closure $condition = true): static
    {
        $this->isCollapsible = $condition;

        return $this;
    }

    public function isCollapsible(): bool
    {
        return (bool) $this->evaluate($this->isCollapsible);
    }

    /**
     * @internal Used by the default table layout, which places the filters form in every position and lets `FiltersLayout` decide which one renders.
     */
    public function position(TableFiltersPosition $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getPosition(): TableFiltersPosition
    {
        return $this->position;
    }

    protected function getPartView(): string
    {
        return 'filament-tables::components.parts.filters';
    }
}
