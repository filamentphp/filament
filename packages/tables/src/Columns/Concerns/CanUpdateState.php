<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

trait CanUpdateState
{
    protected ?Closure $updateStateUsing = null;

    public function updateStateUsing(?Closure $callback): static
    {
        $this->updateStateUsing = $callback;

        return $this;
    }

    public function updateState(mixed $state): mixed
    {
        if (blank($state)) {
            $state = null;
        }

        if ($this->updateStateUsing !== null) {
            return $this->evaluate($this->updateStateUsing, [
                'state' => $state,
            ]);
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
            $record = $record->{$tableRelationship->getPivotAccessor()};
        } else {
            $columnName = (string) str($columnName)->replace('.', '->');
        }

        if (! ($record instanceof Model)) {
            return null;
        }

        $record->setAttribute($columnName, $state);
        $record->save();

        return $state;
    }
}
