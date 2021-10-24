<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait CanSearchRecords
{
    public $tableSearchQuery = '';

    public function isTableSearchable(): bool
    {
        foreach ($this->getCachedTableColumns() as $column) {
            if (! $column->isSearchable()) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function updatedTableSearchQuery()
    {
        $this->selected = [];

        $this->resetPage();
    }

    protected function applySearchToTableQuery(Builder $query): Builder
    {
        if ($this->tableSearchQuery === '' || $this->tableSearchQuery === null) {
            return $query;
        }

        return $query->where(function (Builder $query) {
            $isFirst = true;

            foreach ($this->getCachedTableColumns() as $column) {
                $column->applySearchConstraint($query, $this->tableSearchQuery, $isFirst);
            }
        });
    }

    protected function getTableSearchQuery()
    {
        return strtolower($this->tableSearchQuery);
    }
}
