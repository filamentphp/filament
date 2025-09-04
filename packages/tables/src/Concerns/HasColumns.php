<?php

namespace Filament\Tables\Concerns;

use Closure;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Contracts\Editable;
use Filament\Tables\Columns\Layout\Component as ColumnLayoutComponent;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Renderless;
use ReflectionMethod;

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

    public function updateTableColumnState(string $column, string $record, mixed $input): mixed
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
     * @param  array<string, mixed>  $arguments
     */
    public function callTableColumnMethod(string $name, string $recordKey, string $method, array $arguments = []): mixed
    {
        $column = $this->getTable()->getColumn($name);

        if (! $column) {
            return null;
        }

        if (! method_exists($column, $method)) {
            return null;
        }

        $methodReflection = new ReflectionMethod($column, $method);

        if (! $methodReflection->getAttributes(ExposedLivewireMethod::class)) {
            return null;
        }

        if ($methodReflection->getAttributes(Renderless::class)) {
            $this->skipRender();
        }

        $record = $this->getTableRecord($recordKey);

        if (! $record) {
            return null;
        }

        $column->record($record);

        return $column->{$method}(...$arguments);
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
