<?php

namespace Filament\Tables\Concerns;

use Filament\Forms;
use Filament\Forms\ComponentContainer;
use Filament\Tables\Columns\Column;
use Illuminate\Support\Arr;

/**
 * @property ComponentContainer $tableColumnToggleForm
 */
trait CanToggleColumns
{
    public array $tableColumnToggleStates = [];

    public function prepareTableColumnToggleStates(): void
    {
        // If we have toggled columns already, use those.
        if (count($this->tableColumnToggleStates)) {
            $this->getTableColumnToggleForm()->fill($this->tableColumnToggleStates);

            return;
        }

        // Otherwise, this is a first page load. Set up our defaults.
        $this->getTableColumnToggleForm()->hydrateDefaultState();

        // If no columns are toggled, select our default number of columns.
        if (! $this->tableColumnsToggled()) {
            $this->toggleInitialColumns($this->initialTableColumnsToggled());
        }
    }

    public function hasToggleableTableColumns(): bool
    {
        return $this->table->getLivewire()->getResourceTable()->isToggleable();
    }

    public function tableColumnsToggled(): int
    {
        return count(
            array_filter(
            array_map(fn ($selection) => Arr::get($selection, 'isActive', false), $this->tableColumnToggleStates))
        );
    }

    public function initialTableColumnsToggled(): int
    {
        return $this->table->getLivewire()->getResourceTable()->initialColumnsSelected();
    }

    public function getTableColumnToggleState($key): bool
    {
        return Arr::get($this->tableColumnToggleStates, "{$key}.isActive", false);
    }

    public function getTableColumnToggleForm(): ComponentContainer
    {
        return $this->tableColumnToggleForm;
    }

    protected function getTableColumnToggleFormColumns(): int | array
    {
        return 1;
    }

    protected function getTableColumnToggleFormSchema(): array
    {
        return array_map(
            fn (Column $column) => Forms\Components\Group::make()
                ->schema($column->getToggleFormSchema())
                ->statePath($column->getName()),
            $this->cachedTableColumns,
        );
    }

    protected function toggleInitialColumns(int $count): void
    {
        foreach (array_slice($this->tableColumnToggleStates, 0, $count) as $key => $selection) {
            $this->tableColumnToggleStates[$key]['isActive'] = true;
        }
    }
}
