<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Pagination\LengthAwarePaginator;

trait CanSelectRecords
{
    /**
     * @var array<int | string>
     */
    public array $selectedTableRecords = [];

    protected bool $shouldSelectCurrentPageOnly = false;

    public function deselectAllTableRecords(): void
    {
        $this->emitSelf('deselectAllTableRecords');
    }

    public function getAllTableRecordKeys(): array
    {
        $query = $this->getFilteredTableQuery();

        if ($this->getTable()->selectsCurrentPageOnly()) {
            return $this->getTableRecords()
                ->map(fn ($key): string => (string) $key->getKey())
                ->all();
        }

        return $query
            ->pluck($query->getModel()->getQualifiedKeyName())
            ->map(fn ($key): string => (string) $key)
            ->all();
    }

    public function getAllTableRecordsCount(): int
    {
        if ($this->getTable()->selectsCurrentPageOnly()) {
            return $this->records->count();
        }

        if ($this->records instanceof LengthAwarePaginator) {
            return $this->records->total();
        }

        return $this->getFilteredTableQuery()->count();
    }

    public function getSelectedTableRecords(): Collection
    {
        $table = $this->getTable();

        if (! ($table->getRelationship() instanceof BelongsToMany && $table->allowsDuplicates())) {
            $query = $table->getQuery()->whereIn(app($table->getModel())->getQualifiedKeyName(), $this->selectedTableRecords);
            $this->applySortingToTableQuery($query);

            return $query->get();
        }

        /** @var BelongsToMany $relationship */
        $relationship = $table->getRelationship();

        $pivotClass = $relationship->getPivotClass();
        $pivotKeyName = app($pivotClass)->getKeyName();

        return $this->hydratePivotRelationForTableRecords($table->selectPivotDataInQuery(
            $relationship->wherePivotIn($pivotKeyName, $this->selectedTableRecords),
        )->get());
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    public function shouldSelectCurrentPageOnly(): bool
    {
        return $this->shouldSelectCurrentPageOnly;
    }
}
