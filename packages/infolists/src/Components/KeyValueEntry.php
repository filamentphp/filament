<?php

namespace Filament\Infolists\Components;

use Closure;

class KeyValueEntry extends Entry
{
    use Concerns\CanFormatState;

    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.key-value-entry';

    protected string | Closure | null $keyLabel = null;

    protected string | Closure | null $valueLabel = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);

        $this->formatStateUsing(static function (?array $state) {
            return collect($state ?? [])
                ->filter(static fn (?string $value, ?string $key): bool => filled($key))
                ->map(static fn (?string $value): ?string => filled($value) ? $value : null)
                ->all();
        });
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

    public function getKeyLabel(): string
    {
        return $this->evaluate($this->keyLabel) ?? __('filament-infolists::components.entries.key_value.fields.key.label');
    }

    public function getValueLabel(): string
    {
        return $this->evaluate($this->valueLabel) ?? __('filament-infolists::components.entries.key_value.fields.value.label');
    }
}
