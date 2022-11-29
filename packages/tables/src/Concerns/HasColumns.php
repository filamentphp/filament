<?php

namespace Filament\Tables\Concerns;

use Closure;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Contracts\Editable;
use Filament\Tables\Columns\Layout\Component as ColumnLayoutComponent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\ValidationException;

trait HasColumns
{
    public function callTableColumnAction(string $name, string $recordKey)
    {
        $record = $this->getTableRecord($recordKey);

        if (! $record) {
            return;
        }

        $column = $this->getTable()->getColumn($name);

        if (! $column) {
            return;
        }

        if ($column->isHidden()) {
            return;
        }

        $action = $column->getAction();

        if (! ($action instanceof Closure)) {
            return;
        }

        return $column->record($record)->evaluate($action);
    }

    /**
     * @param  mixed  $input
     * @return array{'error': string} | null
     */
    public function setColumnValue(string $column, string $record, $input): ?array
    {
        $columnName = $column;
        $column = $this->getTable()->getColumn($column);

        if (! ($column instanceof Editable)) {
            return null;
        }

        $record = $this->getTableRecord($record);

        if (! $record) {
            return null;
        }

        $column->record($record);

        if ($column->isDisabled()) {
            return null;
        }

        try {
            $column->validate($input);
        } catch (ValidationException $exception) {
            return [
                'error' => $exception->getMessage(),
            ];
        }

//        if ($columnRelationship = $column->getRelationship($record)) {
//            $record = $columnRelationship->getResults();
//            $columnName = $column->getRelationshipAttribute();
        if ($relationshipRecord = $column->getNestedRecord()) {
            $record = $relationshipRecord;
            $columnName = $column->getRelationshipAttribute();
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

        $record->setAttribute($columnName, $input);
        $record->save();

        return null;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     *
     * @return array<Column | ColumnLayoutComponent>
     */
    protected function getTableColumns(): array
    {
        return [];
    }
}
