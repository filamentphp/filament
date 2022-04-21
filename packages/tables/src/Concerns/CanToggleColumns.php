<?php

namespace Filament\Tables\Concerns;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Checkbox;
use Illuminate\Support\Arr;

/**
 * @property ComponentContainer $toggleTableColumnForm
 */
trait CanToggleColumns
{
    public array $toggledTableColumns = [];

    public function prepareToggledTableColumns(): void
    {
        $this->toggledTableColumns = session()->get(
            $this->getTableColumnToggleFormStateSessionKey(),
            $this->getDefaultTableColumnToggleState()
        );
    }

    protected function getDefaultTableColumnToggleState(): array
    {
        $state = [];

        foreach ($this->getCachedTableColumns() as $column) {
            if (! $column->isToggleable()) {
                continue;
            }

            data_set($state, $column->getName(), ! $column->isToggledHiddenByDefault());
        }

        return $state;
    }

    public function updatedToggledTableColumns(): void
    {
        session()->put([
            $this->getTableColumnToggleFormStateSessionKey() => $this->toggledTableColumns,
        ]);
    }

    public function hasToggleableTableColumns(): bool
    {
        foreach ($this->getCachedTableColumns() as $column) {
            if (! $column->isToggleable()) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function getTableColumnToggleForm(): ComponentContainer
    {
        return $this->toggleTableColumnForm;
    }

    protected function getTableColumnToggleFormSchema(): array
    {
        $schema = [];

        foreach ($this->getCachedTableColumns() as $column) {
            if (! $column->isToggleable()) {
                continue;
            }

            $schema[] = Checkbox::make($column->getName())
                ->label($column->getLabel());
        }

        return $schema;
    }

    protected function getTableColumnToggleFormColumns(): int | array
    {
        return 1;
    }

    public function isTableColumnToggledHidden(string $name): bool
    {
        return Arr::has($this->toggledTableColumns, $name) && ! data_get($this->toggledTableColumns, $name);
    }

    public function getTableColumnToggleFormStateSessionKey(): string
    {
        $table = class_basename($this::class);

        return $table . '_toggled_columns';
    }
}
