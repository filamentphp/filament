<?php

namespace Filament\Tables\Concerns;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use function Filament\Support\get_model_label;

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
            $column->applyRelationshipAggregates($query);
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

    public function getTableModel(): string
    {
        return $this->getTableQuery()->getModel()::class;
    }

    public function getTableRecordKey(Model $record): string
    {
        return $record->getKey();
    }

    public function getTableRecordTitle(Model $record): ?string
    {
        return $record->getKey();
    }

    public function getTableModelLabel(): ?string
    {
        return (string) get_model_label($this->getTableModel());
    }

    public function getTablePluralModelLabel(): ?string
    {
        return Str::plural($this->getTableModelLabel());
    }

    public function getTableRecordTitleAttribute(): ?string
    {
        return null;
    }
}
