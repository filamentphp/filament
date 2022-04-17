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

    public function prepareToggledColumns(): void
    {
        $this->toggledColumns = session()->get($this->getColumnToggleFormStateSessionKey(), []);
    }

    public function updatedToggledColumns(): void
    {
        session()->put([
            $this->getColumnToggleFormStateSessionKey() => $this->toggledColumns,
        ]);
    }

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

    public function isToggled(string $name): bool
    {
        return isset($this->toggledColumns[$name]) && (bool) $this->toggledColumns[$name];
    }

    public function getColumnToggleFormStateSessionKey(): string
    {
        $table = class_basename($this::class);

        return $table . '_toggled_columns';
    }
}
