<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Components\Component;

trait HasStateBindingModifiers
{
    protected $stateBindingModifiers = null;

    protected string | int | null $debounce = null;

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

    public function debounce(string | int | null $delay = '500ms'): static
    {
        $this->debounce = $delay;

        return $this;
    }

    public function stateBindingModifiers(?array $modifiers): static
    {
        $this->stateBindingModifiers = $modifiers;

        return $this;
    }

    public function applyStateBindingModifiers(string $expression): string
    {
        $modifiers = $this->getStateBindingModifiers();

        if (str_starts_with($expression, 'entangle') && (in_array('lazy', $modifiers) || in_array('debounce', $modifiers))) {
            $modifiers = ['defer'];
        }

        return implode('.', array_merge([$expression], $modifiers));
    }

    public function getStateBindingModifiers(): array
    {
        if ($this->stateBindingModifiers !== null) {
            return $this->stateBindingModifiers;
        }

        if ($this->debounce) {
            return ['debounce', $this->debounce];
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
        return in_array('lazy', $this->getStateBindingModifiers());
    }

    public function getDebounce(): string | int | null
    {
        return $this->debounce;
    }
}
