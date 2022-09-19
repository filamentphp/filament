<?php

namespace Filament\Tables\Concerns;

use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Contracts\HasRelationshipTable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Pagination\LengthAwarePaginator;

trait CanSelectRecords
{
    public array $selectedTableRecords = [];

    protected bool $selectionCurrentPageOnly = false;

    public function deselectAllTableRecords(): void
    {
        $this->emitSelf('deselectAllTableRecords');
    }

    public function getAllTableRecordKeys(): array
    {
        $query = $this->getFilteredTableQuery();

        if($this->isSelectionCurrentPageOnly()){
            return $this->getTableRecords()
                    ->map(fn ($key): string => (string) $key->id)
                    ->all();
        }

        return $query
            ->pluck($query->getModel()->getQualifiedKeyName())
            ->map(fn ($key): string => (string) $key)
            ->all();
    }

    public function getAllTableRecordsCount(): int
    {
        if($this->isSelectionCurrentPageOnly()){
            return $this->records->count();
        }

        if ($this->records instanceof LengthAwarePaginator) {
            return $this->records->total();
        }

        return $this->getFilteredTableQuery()->count();
    }

    public function getSelectedTableRecords(): Collection
    {
        if (! ($this instanceof HasRelationshipTable && $this->getRelationship() instanceof BelongsToMany && $this->allowsDuplicates())) {
            $query = $this->getTableQuery()->whereIn(app($this->getTableModel())->getQualifiedKeyName(), $this->selectedTableRecords);
            $this->applySortingToTableQuery($query);

            return $query->get();
        }

        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $pivotClass = $relationship->getPivotClass();
        $pivotKeyName = app($pivotClass)->getKeyName();

        return $this->hydratePivotRelationForTableRecords($this->selectPivotDataInQuery(
            $relationship->wherePivotIn($pivotKeyName, $this->selectedTableRecords),
        )->get());
    }

    public function isTableSelectionEnabled(): bool
    {
        return (bool) count(array_filter(
            $this->getCachedTableBulkActions(),
            fn (BulkAction $action): bool => ! $action->isHidden(),
        ));
    }

    public function isSelectionCurrentPageOnly(){
        return $this->selectionCurrentPageOnly;
    }
}
