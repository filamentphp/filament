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

    public function updatedTableSearchQuery(): void
    {
        $this->deselectAllTableRecords();

        $this->resetPage();
    }

    protected function applySearchToTableQuery(Builder $query): Builder
    {
        $searchQuery = $this->getTableSearchQuery();

        if ($searchQuery === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($searchQuery) {
            $isFirst = true;

            foreach ($this->getCachedTableColumns() as $column) {
                $column->applySearchConstraint($query, $searchQuery, $isFirst);
            }
        });
    }

    protected function getTableSearchQuery(): string
    {
        return trim(strtolower($this->tableSearchQuery));
    }
}
