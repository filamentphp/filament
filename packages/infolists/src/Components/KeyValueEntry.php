<?php

namespace Filament\Infolists\Components;

use Closure;

class KeyValueEntry extends Entry
{
    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.key-value-entry';

    protected string | Closure | null $keyLabel = null;

    protected string | Closure | null $valueLabel = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->placeholder(__('filament-infolists::components.entries.key_value.placeholder'));
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

    /**
     * @deprecated Use `placeholder()` instead.
     */
    public function emptyMessage(string | Closure | null $message): static
    {
        $this->placeholder($message);

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
}
