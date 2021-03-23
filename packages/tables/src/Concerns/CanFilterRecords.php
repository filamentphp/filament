<?php

namespace Filament\Tables\Concerns;

trait CanFilterRecords
{
    public $filter = null;

    public $isFilterable = true;

    public $isSearchable = true;

    public $search = '';

    public function isFilterable()
    {
        return $this->isFilterable && count($this->getTable()->getFilters());
    }

    public function isSearchable()
    {
        return $this->isSearchable && collect($this->getTable()->getColumns())
                ->filter(fn ($column) => $column->isSearchable())
                ->count();
    }

    public function updatedFilter()
    {
        $this->selected = [];

        if (! $this->hasPagination()) {
            return;
        }

        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->selected = [];

        if (! $this->hasPagination()) {
            return;
        }

        $this->resetPage();
    }
}
