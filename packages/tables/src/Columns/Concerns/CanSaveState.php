<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Filament\Tables\Columns\Column;
use Filament\Tables\Contracts\HasRelationshipTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

trait CanSaveState
{
    protected ?Closure $saveStateUsing = null;

    public function bootCanSaveState()
    {
        $this->saveStateUsing(static function (Column $column, string $columnName, HasTable $table, Model $record, $input) {
            if ($columnRelationship = $column->getRelationship($record)) {
                $record = $columnRelationship->getResults();
                $columnName = $column->getRelationshipTitleColumnName();
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

            $record->setAttribute($columnName, $input);
            $record->save();
        });
    }

    public function saveStateUsing(?Closure $callback): static
    {
        $this->saveStateUsing = $callback;

        return $this;
    }

    public function getSaveStateUsing(): ?Closure
    {
        return $this->saveStateUsing;
    }
}
