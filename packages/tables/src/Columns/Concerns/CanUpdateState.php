<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

trait CanUpdateState
{
    // Security: Inline editable columns (`ToggleColumn`, `TextInputColumn`,
    // `SelectColumn`, `CheckboxColumn`) do not automatically check Laravel
    // Model Policies before saving. Only the `disabled()` state is
    // checked. Use `disabled()` with a closure, or use a full edit
    // page / modal action where resource authorization is enforced.

    protected ?Closure $updateStateUsing = null;

    protected ?Closure $beforeStateUpdated = null;

    protected ?Closure $afterStateUpdated = null;

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
            $recordToUpdate = Arr::get(
                $record->load($columnRelationshipName),
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
            return null;
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
