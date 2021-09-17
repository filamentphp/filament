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
        if ($this->getChildComponents() !== []) {
            foreach ($this->getChildComponents() as $component) {
                $component->stateBindingModifiers($modifiers);
            }
        } else {
            $this->stateBindingModifiers = $modifiers;
        }

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
