<?php

namespace Filament\Infolists\Components;

use Closure;
use Illuminate\Support\Collection;

class KeyValueEntry extends Entry
{
    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.key-value-entry';

    protected string | Closure | null $keyLabel = null;

    protected string | Closure | null $valueLabel = null;

    protected string | Closure | null $emptyLabel = null;

    public function getState(): mixed
    {
        $state = parent::getState();

        if ($state === null) {
            return [];
        }

        if (is_array($state)) {
            return collect($state);
        }

        return $state;
    }

    public function keyLabel(string | Closure | null $label): static
    {
        $this->keyLabel = $label;

        return $this;
    }

    public function valueLabel(string | Closure | null $label): static
    {
        $this->valueLabel = $label;

        return $this;
    }

    public function emptyLabel(string | Closure | null $label): static
    {
        $this->emptyLabel = $label;

        return $this;
    }

    public function getKeyLabel(): string
    {
        return $this->evaluate($this->keyLabel) ?? __('filament-infolists::components.entries.key_value.columns.key.label');
    }

    public function getValueLabel(): string
    {
        return $this->evaluate($this->valueLabel) ?? __('filament-infolists::components.entries.key_value.columns.value.label');
    }

    public function getEmptyLabel(): string
    {
        return $this->evaluate($this->emptyLabel) ?? __('filament-infolists::components.entries.key_value.empty.label');
    }
}
