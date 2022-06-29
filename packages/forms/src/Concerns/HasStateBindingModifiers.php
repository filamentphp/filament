<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Components\Component;

trait HasStateBindingModifiers
{
    protected $stateBindingModifiers = null;

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

    public function stateBindingModifiers(array $modifiers): static
    {
        $this->stateBindingModifiers = $modifiers;

        return $this;
    }

    public function applyStateBindingModifiers($expression, ?string $except = null): string
    {
        $modifiers = $this->getStateBindingModifiers();

        if ($except) {
            unset($modifiers[array_search($except, $modifiers, false)]);
        }

        return implode('.', array_merge([$expression], $modifiers));
    }

    public function getStateBindingModifiers(): array
    {
        if ($this->stateBindingModifiers !== null) {
            return $this->stateBindingModifiers;
        }

        if ($this instanceof Component) {
            return $this->getContainer()->getStateBindingModifiers();
        }

        if ($this->getParentComponent()) {
            return $this->getParentComponent()->getStateBindingModifiers();
        }

        return ['defer'];
    }

    public function isLazy(): bool
    {
        return in_array('lazy', $this->getStateBindingModifiers(), false);
    }
}
