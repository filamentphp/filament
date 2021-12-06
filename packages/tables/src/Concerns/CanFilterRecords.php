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

        if (blank($this->filter) && ($defaultFilter = $this->getTable()->getDefaultFilter())) {
            $this->filter = $defaultFilter;
        }

        if (blank($this->filter)) {
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
