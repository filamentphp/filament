<?php

namespace Filament\Tables\Concerns;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait HasRecords
{
    protected Collection | Paginator | null $records = null;

    protected function getFilteredTableQuery(): Builder
    {
        $query = $this->getTableQuery();

        $this->applyFiltersToTableQuery($query);

        $this->applySearchToTableQuery($query);

        foreach ($this->getCachedTableColumns() as $column) {
            $column->applyEagerLoading($query);
            $column->applyRelationshipCount($query);
        }

        return $query;
    }

    public function getTableRecords(): Collection | Paginator
    {
        if ($this->records) {
            return $this->records;
        }

        $query = $this->getFilteredTableQuery();

        $this->applySortingToTableQuery($query);

        $this->records = $this->isTablePaginationEnabled() ?
            $this->paginateTableQuery($query) :
            $query->get();

        return $this->records;
    }

    protected function resolveTableRecord(?string $key): ?Model
    {
        return $this->getTableQuery()->find($key);
    }
}
