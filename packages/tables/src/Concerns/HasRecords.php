<?php

namespace Filament\Tables\Concerns;

use function Filament\locale_has_pluralization;
use function Filament\Support\get_model_label;
use Filament\Tables\Contracts\HasRelationshipTable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use function Livewire\invade;

trait HasRecords
{
    protected bool $allowsDuplicates = false;

    protected Collection | Paginator | null $records = null;

    public function allowsDuplicates(): bool
    {
        return $this->allowsDuplicates;
    }

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

    protected function hydratePivotRelationForTableRecords(Collection | Paginator $records): Collection | Paginator
    {
        if ($this instanceof HasRelationshipTable && $this->getRelationship() instanceof BelongsToMany && ! $this->allowsDuplicates()) {
            invade($this->getRelationship())->hydratePivotRelation($records->all());
        }

        return $records;
    }

    public function getTableRecords(): Collection | Paginator
    {
        if ($this->records) {
            return $this->records;
        }

        $query = $this->getFilteredTableQuery();

        $this->applySortingToTableQuery($query);

        if (
            (! $this->isTablePaginationEnabled()) ||
            ($this->isTableReordering() && (! $this->isTablePaginationEnabledWhileReordering()))
        ) {
            return $this->records = $this->hydratePivotRelationForTableRecords($query->get());
        }

        return $this->records = $this->hydratePivotRelationForTableRecords($this->paginateTableQuery($query));
    }

    protected function resolveTableRecord(?string $key): ?Model
    {
        if ($key === null) {
            return null;
        }

        if (! ($this instanceof HasRelationshipTable && $this->getRelationship() instanceof BelongsToMany)) {
            return $this->getTableQuery()->find($key);
        }

        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $pivotClass = $relationship->getPivotClass();
        $pivotKeyName = app($pivotClass)->getKeyName();

        $query = $this->allowsDuplicates() ?
            $relationship->wherePivot($pivotKeyName, $key) :
            $relationship->where($relationship->getQualifiedRelatedKeyName(), $key);

        $record = $this->selectPivotDataInQuery($query)->first();

        return $record?->setRawAttributes($record->getRawOriginal());
    }

    public function getTableModel(): string
    {
        return $this->getTableQuery()->getModel()::class;
    }

    public function getTableRecord(?string $key): ?Model
    {
        return $this->resolveTableRecord($key);
    }

    public function getTableRecordKey(Model $record): string
    {
        if (! ($this instanceof HasRelationshipTable && $this->getRelationship() instanceof BelongsToMany && $this->allowsDuplicates())) {
            return $record->getKey();
        }

        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $pivotClass = $relationship->getPivotClass();
        $pivotKeyName = app($pivotClass)->getKeyName();

        return $record->getAttributeValue($pivotKeyName);
    }

    public function getTableRecordTitle(Model $record): string
    {
        return $this->getTableModelLabel();
    }

    public function getTableModelLabel(): string
    {
        return (string) get_model_label($this->getTableModel());
    }

    public function getTablePluralModelLabel(): string
    {
        if (locale_has_pluralization()) {
            return Str::plural($this->getTableModelLabel());
        }

        return $this->getTableModelLabel();
    }
}
