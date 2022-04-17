<?php

namespace Filament\Tables\Columns\Concerns;

use Filament\Forms\Components\Checkbox;

trait CanBeSelected
{
    protected bool $isAlwaysSelected = false;

    protected bool $isInitiallySelected = false;

    public function selected(bool $condition = true): static
    {
        $this->isInitiallySelected = $condition;

        return $this;
    }

    public function alwaysSelected(bool $condition = true): static
    {
        $this->isAlwaysSelected = $condition;

        return $this;
    }

    public function isSelected(): bool
    {
        return ! $this->getTable()->isToggleable() || $this->getTable()->getLivewire()->getTableColumnToggleState($this->getName());
    }

    public function getToggleFormSchema(): array
    {
        return [
            Checkbox::make('isActive')
                ->label($this->getLabel())
                ->disabled($this->isAlwaysSelected)
                ->extraAttributes(['class' => $this->isAlwaysSelected ? 'opacity-50' : ''])
                ->default($this->isInitiallySelected || $this->isAlwaysSelected),
        ];
    }
}
