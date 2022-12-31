<?php

namespace Filament\Tables\Concerns;

use Closure;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Contracts\Editable;
use Filament\Tables\Columns\Layout\Component as ColumnLayoutComponent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Filament\Tables\Columns\Layout\Component;
use Illuminate\Validation\ValidationException;

trait HasColumns
{
    public function callTableColumnAction(string $name, string $recordKey): mixed
    {
        $record = $this->getTableRecord($recordKey);

        if (! $record) {
            return null;
        }

        $column = $this->getTable()->getColumn($name);

        if (! $column) {
            return null;
        }

        if ($column->isHidden()) {
            return null;
        }

        $action = $column->getAction();

        if (! ($action instanceof Closure)) {
            return null;
        }

        return $column->record($record)->evaluate($action);
    }

    public function getCachedTableColumns(): array
    {
        return $this->cachedTableColumns;
    }

    public function getCachedTableColumnsLayout(): array
    {
        return $this->cachedTableColumnsLayout;
    }

    public function getCachedCollapsibleTableColumnsLayout(): ?Component
    {
        return $this->cachedTableCollapsibleColumnsLayout;
    }

    public function hasTableColumnsLayout(): bool
    {
        return $this->hasTableColumnsLayout || $this->getTableContentGrid();
    }

    public function getCachedTableColumn(string $name): ?Column
    {
        return $this->getCachedTableColumns()[$name] ?? null;
    }

    public function updateTableColumnState(string $column, string $record, $input): mixed
    {
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

        return $column->updateState($input);
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
