<?php

namespace Filament\Tables\Columns;

use Closure;

class IconColumn extends Column
{
    use Concerns\HasColors;

    protected string $view = 'tables::columns.icon-column';

    protected array $options = [];

    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getStateIcon(): ?string
    {
        $state = $this->getState();
        $stateIcon = null;

        foreach ($this->getOptions() as $icon => $condition) {
            if (is_numeric($icon)) {
                $stateIcon = $condition;
            } elseif ($condition instanceof Closure && $condition($state)) {
                $stateIcon = $icon;
            } elseif ($condition === $state) {
                $stateIcon = $icon;
            }
        }

        return $stateIcon;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
