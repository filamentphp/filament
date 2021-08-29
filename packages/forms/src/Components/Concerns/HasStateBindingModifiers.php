<?php

namespace Filament\Forms\Components\Concerns;

trait HasStateBindingModifiers
{
    protected array $stateBindingModifiers = ['defer'];

    public function reactive(): static
    {
        $this->stateBindingModifiers([]);

        return $this;
    }

    public function lazy(): static
    {
        $this->stateBindingModifiers(['lazy']);

        return $this;
    }

    public function stateBindingModifiers(array | callable $modifiers): static
    {
        $this->stateBindingModifiers = $modifiers;

        return $this;
    }

    public function applyStateBindingModifiers($expression): string
    {
        $modifiers = $this->getStateBindingModifiers();

        return implode('.', array_merge([$expression], $modifiers));
    }

    public function getStateBindingModifiers(): array
    {
        return $this->evaluate($this->stateBindingModifiers);
    }
}
