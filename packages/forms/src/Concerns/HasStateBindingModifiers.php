<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Components\Component;

trait HasStateBindingModifiers
{
    /**
     * @var array<string> | null
     */
    protected ?array $stateBindingModifiers = null;

    protected int | string | null $liveDebounce = null;

    protected ?bool $isLive = null;

    protected bool $isLiveOnBlur = false;

    public function live(bool $onBlur = false, int | string | null $debounce = null, ?bool $condition = true): static
    {
        $this->isLive = $condition;
        $this->isLiveOnBlur = $onBlur;
        $this->liveDebounce = $debounce;

        return $this;
    }

    public function reactive(): static
    {
        $this->live();

        return $this;
    }

    public function lazy(): static
    {
        $this->live(onBlur: true);

        return $this;
    }

    public function debounce(int | string | null $delay = 500): static
    {
        $this->live(debounce: $delay);

        return $this;
    }

    /**
     * @param  array<string> | null  $modifiers
     */
    public function stateBindingModifiers(?array $modifiers): static
    {
        $this->stateBindingModifiers = $modifiers;

        return $this;
    }

    public function applyStateBindingModifiers(string $expression, bool $isOptimisticallyLive = true): string
    {
        $entangled = str($expression)->is('$entangle(*)');

        $modifiers = $this->getStateBindingModifiers(withBlur: ! $entangled, withDebounce: ! $entangled, isOptimisticallyLive: $isOptimisticallyLive);

        if ($entangled) {
            return (string) str($expression)->replaceLast(
                ')',
                in_array('live', $modifiers) ? ', true)' : ', false)',
            );
        }

        return implode('.', [
            $expression,
            ...$modifiers,
        ]);
    }

    /**
     * @return array<string>
     */
    public function getStateBindingModifiers(bool $withBlur = true, bool $withDebounce = true, bool $isOptimisticallyLive = true): array
    {
        if ($this->stateBindingModifiers !== null) {
            return $this->stateBindingModifiers;
        }

        if ($this->isLive === false) {
            return [];
        }

        if ($this->isLiveOnBlur) {
            if (! $withBlur) {
                return $isOptimisticallyLive ? ['live'] : [];
            }

            return ['blur'];
        }

        if (filled($this->liveDebounce)) {
            if (! $withDebounce) {
                return $isOptimisticallyLive ? ['live'] : [];
            }

            return ['live', 'debounce', $this->liveDebounce];
        }

        if ($this->isLive) {
            return ['live'];
        }

        if ($this instanceof Component) {
            return $this->getContainer()->getStateBindingModifiers($withBlur, $withDebounce, $isOptimisticallyLive);
        }

        if ($this->getParentComponent()) {
            return $this->getParentComponent()->getStateBindingModifiers($withBlur, $withDebounce, $isOptimisticallyLive);
        }

        return [];
    }

    public function isLive(): bool
    {
        if ($this->isLive !== null) {
            return $this->isLive;
        }

        if ($this instanceof Component) {
            return $this->getContainer()->isLive();
        }

        if ($this->getParentComponent()) {
            return $this->getParentComponent()->isLive();
        }

        return false;
    }

    public function isLiveOnBlur(): bool
    {
        if ($this->isLive !== null) {
            return $this->isLiveOnBlur;
        }

        if ($this instanceof Component) {
            return $this->getContainer()->isLiveOnBlur();
        }

        if ($this->getParentComponent()) {
            return $this->getParentComponent()->isLiveOnBlur();
        }

        return false;
    }

    public function isLazy(): bool
    {
        return $this->isLiveOnBlur();
    }

    public function isLiveDebounced(): bool
    {
        if ($this->isLiveOnBlur) {
            return false;
        }

        if (filled($this->liveDebounce)) {
            return true;
        }

        if ($this instanceof Component) {
            return $this->getContainer()->isLiveDebounced();
        }

        if ($this->getParentComponent()) {
            return $this->getParentComponent()->isLiveDebounced();
        }

        return false;
    }

    public function getLiveDebounce(): int | string | null
    {
        if ($this->isLiveOnBlur) {
            return null;
        }

        if (filled($this->liveDebounce)) {
            return $this->liveDebounce;
        }

        if ($this instanceof Component) {
            return $this->getContainer()->getLiveDebounce();
        }

        if ($this->getParentComponent()) {
            return $this->getParentComponent()->getLiveDebounce();
        }

        return null;
    }

    public function getNormalizedLiveDebounce(): ?int
    {
        $debounce = $this->getLiveDebounce();

        if (! $debounce) {
            return null;
        }

        if (is_numeric($debounce)) {
            return (int) $debounce;
        }

        if (str($debounce)->endsWith('ms')) {
            return (int) (string) str($debounce)->beforeLast('ms');
        }

        if (str($debounce)->endsWith('s')) {
            return ((int) (string) str($debounce)->beforeLast('s')) * 1000;
        }

        return preg_replace('/[^0-9]/', '', $debounce) ?: 0;
    }
}
