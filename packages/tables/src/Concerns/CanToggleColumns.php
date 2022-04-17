<?php

namespace Filament\Tables\Concerns;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Checkbox;

/**
 * @property ComponentContainer $toggleColumnForm
 */
trait CanToggleColumns
{
    public array $toggledColumns = [];

    public function hasToggleableColumns(): bool
    {
        foreach ($this->getCachedTableColumns() as $column) {
            if (! $column->isToggleable()) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function getColumnToggleForm(): ComponentContainer
    {
        return $this->toggleColumnForm;
    }

    protected function getColumnToggleFormSchema(): array
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

    protected function getColumnToggleFormColumns(): int | array
    {
        return 1;
    }
}
