<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

trait CanUpdateState
{
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

        $this->callBeforeStateUpdated($state);

        if ($this->updateStateUsing !== null) {
            try {
                return $this->evaluate($this->updateStateUsing, [
                    'state' => $state,
                ]);
            } finally {
                $this->callAfterStateUpdated($state);
            }
        }

        $record = $this->getRecord();

        $columnName = $this->getName();

        if ($this->getRelationship($record)) {
            $columnName = $this->getRelationshipAttribute();
            $columnRelationshipName = $this->getRelationshipName();

            $record = Arr::get(
                $record->load($columnRelationshipName),
                $columnRelationshipName,
            );
        } elseif (
            (($tableRelationship = $this->getTable()->getRelationship()) instanceof BelongsToMany) &&
            in_array($columnName, $tableRelationship->getPivotColumns())
        ) {
            $record = $record->getRelationValue($tableRelationship->getPivotAccessor());
        } else {
            $columnName = (string) str($columnName)->replace('.', '->');
        }

        if (! ($record instanceof Model)) {
            return null;
        }

        $record->setAttribute($columnName, $state);
        $record->save();

        $this->callAfterStateUpdated($state);

        return $state;
    }

    public function callBeforeStateUpdated(mixed $state): mixed
    {
        return $this->evaluate($this->beforeStateUpdated, ['state' => $state]);
    }

    public function callAfterStateUpdated(mixed $state): mixed
    {
        return $this->evaluate($this->afterStateUpdated, ['state' => $state]);
    }
}
