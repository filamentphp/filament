<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Filament\Tables\Contracts\HasRelationshipTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Livewire\Component;

trait CanSaveState
{
    protected ?Closure $saveStateUsing = null;

    public function saveStateUsing(?Closure $callback): static
    {
        $this->saveStateUsing = $callback;

        return $this;
    }

    public function saveState(Component $table, mixed $state): mixed
    {
        if ($this->saveStateUsing !== null) {
            return $this->evaluate($this->saveStateUsing, [
                'state' => $state,
                'table' => $table,
            ]);
        }

        $record = $this->getRecord();

        $columnName = $this->getName();

        if ($columnRelationship = $this->getRelationship($record)) {
            $record = $columnRelationship->getResults();
            $columnName = $this->getRelationshipTitleColumnName();
        } elseif (
            $table instanceof HasRelationshipTable &&
            (($tableRelationship = $table->getRelationship()) instanceof BelongsToMany) &&
            in_array($columnName, $tableRelationship->getPivotColumns())
        ) {
            $record = $record->{$tableRelationship->getPivotAccessor()};
        } else {
            $columnName = (string) Str::of($columnName)->replace('.', '->');
        }

        if (! ($record instanceof Model)) {
            return null;
        }

        $record->setAttribute($columnName, $state);
        $record->save();

        return $state;
    }
}
