<?php

namespace Filament\Tables\Concerns;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

trait HasRecords
{
    protected Collection | Paginator | null $records = null;

    protected function getFilteredTableQuery(): Builder
    {
        $query = $this->getTableQuery();

        $this->applyFiltersToTableQuery($query);

        $this->applySearchToTableQuery($query);

        return $query;
    }

    public function getTableRecords(): Collection | Paginator
    {
        if ($this->records) {
            return $this->records;
        }

        $query = $this->getFilteredTableQuery();

        foreach ($this->getCachedTableColumns() as $column) {
            $column->applyEagreLoading($query);
            $column->applyRelationshipCount($query);
        }

        $this->applySortingToTableQuery($query);

        if ($this->isTablePaginationEnabled()) {
            /** @var LengthAwarePaginator $records */
            $records = $query->paginate(
                $this->getTableRecordsPerPage(),
                ['*'],
                $this->getTablePaginationPageName(),
            );

            $records->onEachSide(1);

            $this->records = $records;
        } else {
            $this->records = $query->get();
        }

        return $this->records;
    }

    protected function resolveTableRecord(?string $key): ?Model
    {
        return $this->getTableQuery()->find($key);
    }
}
