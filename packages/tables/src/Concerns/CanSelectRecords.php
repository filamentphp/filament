<?php

namespace Filament\Tables\Concerns;

use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\RecordCheckboxPosition;
use Filament\Tables\Contracts\HasRelationshipTable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait CanSelectRecords
{
    public array $selectedTableRecords = [];

    protected bool $shouldSelectCurrentPageOnly = false;

    protected EloquentCollection $cachedSelectedTableRecords;

    public function deselectAllTableRecords(): void
    {
        $this->emitSelf('deselectAllTableRecords');
    }

    public function shouldDeselectAllRecordsWhenTableFiltered(): bool
    {
        return true;
    }

    public function getAllSelectableTableRecordKeys(): array
    {
        $query = $this->getFilteredTableQuery();
        $checkIfRecordIsSelectableUsing = $this->isTableRecordSelectable();

        if (! $checkIfRecordIsSelectableUsing) {
            $records = $this->shouldSelectCurrentPageOnly() ?
                $this->getTableRecords() :
                $query;

            return $records
                ->pluck($query->getModel()->getQualifiedKeyName())
                ->map(fn ($key): string => (string) $key)
                ->all();
        }

        $records = $this->shouldSelectCurrentPageOnly() ?
            $this->getTableRecords() :
            $query->get();

        return $records->reduce(
            function (array $carry, Model $record) use ($checkIfRecordIsSelectableUsing): array {
                if (! $checkIfRecordIsSelectableUsing($record)) {
                    return $carry;
                }

                $carry[] = (string) $record->getKey();

                return $carry;
            },
            initial: [],
        );
    }

    public function getAllSelectableTableRecordsCount(): int
    {
        if ($this->isTableRecordSelectable() !== null) {
            /** @var Collection $records */
            $records = $this->shouldSelectCurrentPageOnly() ?
                $this->getTableRecords() :
                $this->getFilteredTableQuery()->get();

            return $records
                ->filter(fn (Model $record): bool => $this->isTableRecordSelectable()($record))
                ->count();
        }

        if ($this->shouldSelectCurrentPageOnly()) {
            return $this->records->count();
        }

        if ($this->records instanceof LengthAwarePaginator) {
            return $this->records->total();
        }

        return $this->getFilteredTableQuery()->count();
    }

    public function getAllTableRecordsCount(): int
    {
        if ($this->records instanceof LengthAwarePaginator) {
            return $this->records->total();
        }

        return $this->getFilteredTableQuery()->count();
    }

    public function getSelectedTableRecords(): EloquentCollection
    {
        if (isset($this->cachedSelectedTableRecords)) {
            return $this->cachedSelectedTableRecords;
        }

        if (! ($this instanceof HasRelationshipTable && $this->getRelationship() instanceof BelongsToMany && $this->allowsDuplicates())) {
            $query = $this->getTableQuery()->whereIn(app($this->getTableModel())->getQualifiedKeyName(), $this->selectedTableRecords);

            foreach ($this->getCachedTableColumns() as $column) {
                $column->applyEagerLoading($query);
                $column->applyRelationshipAggregates($query);
            }

            $this->applySortingToTableQuery($query);

            return $this->cachedSelectedTableRecords = $query->get();
        }

        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $pivotClass = $relationship->getPivotClass();
        $pivotKeyName = app($pivotClass)->getKeyName();

        $relationship->wherePivotIn($pivotKeyName, $this->selectedTableRecords);

        foreach ($this->getCachedTableColumns() as $column) {
            $column->applyEagerLoading($relationship);
            $column->applyRelationshipAggregates($relationship);
        }

        return $this->cachedSelectedTableRecords = $this->hydratePivotRelationForTableRecords(
            $this->selectPivotDataInQuery($relationship)->get(),
        );
    }

    public function isTableSelectionEnabled(): bool
    {
        return (bool) count(array_filter(
            $this->getCachedTableBulkActions(),
            fn (BulkAction $action): bool => ! $action->isHidden(),
        ));
    }

    public function getTableRecordCheckboxPosition(): string
    {
        return RecordCheckboxPosition::BeforeCells;
    }

    public function shouldSelectCurrentPageOnly(): bool
    {
        return $this->shouldSelectCurrentPageOnly;
    }
}
