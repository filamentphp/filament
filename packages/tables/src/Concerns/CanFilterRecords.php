<?php

namespace Filament\Tables\Concerns;

trait CanFilterRecords
{
    public $filter;

    public $isFilterable = true;

    public function isFilterable()
    {
        return $this->isFilterable && count($this->getTable()->getFilters());
    }

    public function updatedFilter()
    {
        $this->selected = [];

        if (! $this->hasPagination()) {
            return;
        }

        $this->resetPage();
    }

    protected function applyFilters($query)
    {
        if (! $this->isFilterable()) {
            return $query;
        }

        // Check if we have a default filter when no filter is selected
        if (($this->filter === '' || $this->filter === null) &&
            ($defaultFilter = $this->getTable()->getDefaultFilter())
        ) {
            $this->filter = $defaultFilter->getName();
        }

        // We don't have any filter selected and no default one
        if ($this->filter === '' || $this->filter === null) {
            return $query;
        }

        collect($this->getTable()->getFilters())
            ->filter(fn ($filter) => $filter->getName() === $this->filter)
            ->each(function ($filter) use (&$query) {
                $query = $filter->apply($query);
            });

        return $query;
    }
}
