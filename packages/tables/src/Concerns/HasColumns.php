<?php

namespace Filament\Tables\Concerns;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Contracts\Editable;
use Filament\Tables\Columns\Layout\Component;
use Illuminate\Validation\ValidationException;

trait HasColumns
{
    protected array $cachedTableColumns;

    protected array $cachedTableColumnsLayout;

    protected ?Component $cachedTableCollapsibleColumnsLayout = null;

    protected bool $hasTableColumnsLayout = false;

    public function cacheTableColumns(): void
    {
        $this->cachedTableColumns = [];
        $this->cachedTableColumnsLayout = [];

        $components = Action::configureUsing(
            Closure::fromCallable([$this, 'configureTableAction']),
            fn (): array => $this->getTableColumns(),
        );

        foreach ($components as $component) {
            $component->table($this->getCachedTable());

            if ($component instanceof Component && $component->isCollapsible()) {
                $this->cachedTableCollapsibleColumnsLayout = $component;
            } else {
                $this->cachedTableColumnsLayout[] = $component;
            }

            if ($component instanceof Column) {
                $this->cachedTableColumns[$component->getName()] = $component;

                continue;
            }

            $this->hasTableColumnsLayout = true;
            $this->cachedTableColumns = array_merge($this->cachedTableColumns, $component->getColumns());
        }
    }

    public function callTableColumnAction(string $name, string $recordKey)
    {
        $record = $this->getTableRecord($recordKey);

        if (! $record) {
            return;
        }

        $column = $this->getCachedTableColumn($name);

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
        $column = $this->getCachedTableColumn($column);

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

    protected function getTableColumns(): array
    {
        return [];
    }
}
