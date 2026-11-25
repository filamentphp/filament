<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

use function Livewire\invade;

trait CanUpdateState
{
    // Security: Inline editing requires explicit `authorize()` configuration
    // to restrict access, even in strict authorization mode.

    protected ?Closure $updateStateUsing = null;

    protected ?Closure $beforeStateUpdated = null;

    protected ?Closure $afterStateUpdated = null;

    protected bool | Closure $isPartiallyRenderedAfterStateUpdated = false;

    /**
     * @var array<string>
     */
    protected array $columnsToPartiallyRenderAfterStateUpdated = [];

    public function updateStateUsing(?Closure $callback): static
    {
        $this->updateStateUsing = $callback;

        return $this;
    }

    public function beforeStateUpdated(?Closure $callback): static
    {
        $this->beforeStateUpdated = $callback;

        return $this;
    }

    public function afterStateUpdated(?Closure $callback): static
    {
        $this->afterStateUpdated = $callback;

        return $this;
    }

    public function partiallyRenderAfterStateUpdated(bool | Closure $condition = true): static
    {
        $this->isPartiallyRenderedAfterStateUpdated = $condition;

        return $this;
    }

    /**
     * @param  array<string>  $columns
     */
    public function partiallyRenderColumnsAfterStateUpdated(array $columns): static
    {
        $this->columnsToPartiallyRenderAfterStateUpdated = $columns;

        return $this;
    }

    public function isPartiallyRenderedAfterStateUpdated(): bool
    {
        return (bool) $this->evaluate($this->isPartiallyRenderedAfterStateUpdated);
    }

    /**
     * @return array<string>
     */
    public function getColumnsToPartiallyRenderAfterStateUpdated(): array
    {
        return $this->columnsToPartiallyRenderAfterStateUpdated;
    }

    /**
     * @return array<string>
     */
    public function getPartiallyRenderableColumnNamesAfterStateUpdated(): array
    {
        if (filled($this->columnsToPartiallyRenderAfterStateUpdated)) {
            return [$this->getName(), ...$this->columnsToPartiallyRenderAfterStateUpdated];
        }

        if ($this->isPartiallyRenderedAfterStateUpdated === false) {
            return [];
        }

        return [$this->getName()];
    }

    /**
     * @return array<string, string>
     */
    public function getUpdateStateMessages(): array
    {
        return [
            'saving' => __('filament-tables::table.columns.editable.saving'),
            'saved' => __('filament-tables::table.columns.editable.saved'),
            'failure' => __('filament-tables::table.columns.editable.failure'),
        ];
    }

    public function updateState(mixed $state): mixed
    {
        if (blank($state)) {
            $state = null;
        }

        $record = $this->getRecord();
        $relatedRecord = null;
        $columnName = $this->getName();
        $hasRelationship = ($record instanceof Model) && $this->hasRelationship($record);

        if ($hasRelationship) {
            $columnName = $this->getFullAttributeName($record);
            $columnRelationshipName = $this->getRelationshipName($record);
            $relatedRecord = Arr::get(
                $record->loadMissing($columnRelationshipName),
                $columnRelationshipName,
            );

            if (! ($relatedRecord instanceof Model)) {
                $relatedRecord = null;
            }
        }

        $this->callBeforeStateUpdated($state, $relatedRecord);

        if ($this->updateStateUsing !== null) {
            try {
                return $this->evaluate($this->updateStateUsing, [
                    'state' => $state,
                    'relatedRecord' => $relatedRecord,
                ]);
            } finally {
                $this->callAfterStateUpdated($state, $relatedRecord);
            }
        }

        $recordToUpdate = $record;

        if ($hasRelationship) {
            $columnName = $this->getFullAttributeName($record);
            $columnRelationshipName = $this->getRelationshipName($record);
            $relationshipsToLoad = [$columnRelationshipName];

            // Match record resolution without adding display-column eager loads.
            if ($this->hasTable() && ($query = $this->getTable()->getQuery(isResolvingRecord: true))) {
                $query = invade($this->getLivewire())->applyFiltersToTableQuery($query, isResolvingRecord: true);

                foreach ($query->applyScopes()->getEagerLoads() as $relationshipName => $constraints) {
                    if (($relationshipName === $columnRelationshipName) || str_starts_with($columnRelationshipName, "{$relationshipName}.")) {
                        $relationshipsToLoad[$relationshipName] = $constraints;
                    }
                }
            }

            $recordToUpdate = Arr::get(
                $record->load($relationshipsToLoad),
                $columnRelationshipName,
            );
            $relatedRecord = $recordToUpdate instanceof Model ? $recordToUpdate : null;
        }

        if ((! $hasRelationship) && ($record instanceof Model) && (
            (($tableRelationship = $this->getTable()->getRelationship()) instanceof BelongsToMany) &&
            in_array($this->getAttributeName($record), $tableRelationship->getPivotColumns())
        )) {
            $recordToUpdate = $record->getRelationValue($tableRelationship->getPivotAccessor());
        }

        if (! ($recordToUpdate instanceof Model)) {
            return ['error' => __('filament-tables::table.columns.editable.unavailable')];
        }

        $recordToUpdate->setAttribute((string) str($columnName)->replace('.', '->'), $state);
        $recordToUpdate->save();

        $this->callAfterStateUpdated($state, $relatedRecord);

        return $state;
    }

    public function callBeforeStateUpdated(mixed $state, ?Model $relatedRecord = null): mixed
    {
        return $this->evaluate($this->beforeStateUpdated, [
            'state' => $state,
            'relatedRecord' => $relatedRecord,
        ]);
    }

    public function callAfterStateUpdated(mixed $state, ?Model $relatedRecord = null): mixed
    {
        return $this->evaluate($this->afterStateUpdated, [
            'state' => $state,
            'relatedRecord' => $relatedRecord,
        ]);
    }
}
