<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Components\Component;
use Illuminate\Support\Str;

trait HasStateBindingModifiers
{
    protected $stateBindingModifiers = null;

    protected string | int | null $debounce = null;

    public function reactive(bool $condition = true): static
    {
        if ($condition) {
            $this->stateBindingModifiers([]);
        }

        return $this;
    }

    public function lazy(bool $condition = true): static
    {
        if ($condition) {
            $this->stateBindingModifiers(['lazy']);
        }

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

    public function applyStateBindingModifiers(string $expression, array $lazilyEntangledModifiers = []): string
    {
        $modifiers = $this->getStateBindingModifiers();

        if (Str::of($expression)->contains('entangle') && ($this->isLazy() || $this->getDebounce())) {
            $modifiers = $lazilyEntangledModifiers;
        }

        return implode('.', array_merge([$expression], $modifiers));
    }

    public function getStateBindingModifiers(): array
    {
        if ($this->stateBindingModifiers !== null) {
            return $this->stateBindingModifiers;
        }

        if ($debounce = $this->getDebounce()) {
            return ['debounce', $debounce];
        }

        if ($this instanceof Component) {
            return $this->getContainer()->getStateBindingModifiers();
        }

        if ($this->getParentComponent()) {
            return $this->getParentComponent()->getStateBindingModifiers();
        }

        return ['defer'];
    }

    public function isReactive(): bool
    {
        return empty($this->getStateBindingModifiers());
    }

    public function isLazy(): bool
    {
        return in_array('lazy', $this->getStateBindingModifiers());
    }

    public function isDebounced(): bool
    {
        return in_array('debounce', $this->getStateBindingModifiers());
    }

    public function getDebounce(): string | int | null
    {
        return $this->debounce;
    }
}
