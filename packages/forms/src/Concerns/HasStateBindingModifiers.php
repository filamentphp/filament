<?php

namespace Filament\Forms\Concerns;

use App\Models\Shop\Product;
use Filament\Forms\Components\Component;
use InvalidArgumentException;

trait HasStateBindingModifiers
{
    /**
     * @var array<string> | null
     */
    protected ?array $stateBindingModifiers = null;

    protected int | string | null $debounce = null;

    protected bool $isLive = false;

    protected bool $isBlur = false;

    public function live(bool $onBlur = false, int | string | null $debounce = null, int | string | null $throttle = null): static
    {
        $this->isLive = true;
        $this->isBlur = $onBlur;
        $this->debounce = $debounce;

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

    public function applyStateBindingModifiers(string $expression): string
    {
        $entangled = str($expression)->contains('entangle');

        $modifiers = $this->getStateBindingModifiers(withBlur: ! $entangled, withDebounce: ! $entangled);

        return implode('.', [
            $expression,
            ...$modifiers,
        ]);
    }

    /**
     * @return array<string>
     */
    public function getStateBindingModifiers(bool $withBlur = true, bool $withDebounce = true): array
    {
        if ($this->stateBindingModifiers !== null) {
            return $this->stateBindingModifiers;
        }

        if ($withDebounce && filled($debounce = $this->getDebounce())) {
            if ($this->isBlur()) {
                throw new InvalidArgumentException('A field cannot use [debounce()] and [blur()] at the same time.');
            }

            return ['live', 'debounce', $debounce];
        }

        if ($withBlur && $this->isBlur()) {
            return ['blur'];
        }

        if ($this->isLive()) {
            return ['live'];
        }

        if ($this instanceof Component) {
            return $this->getContainer()->getStateBindingModifiers();
        }

        if ($this->getParentComponent()) {
            return $this->getParentComponent()->getStateBindingModifiers();
        }

        return [];
    }

    public function isLive(): bool
    {
        return $this->isLive;
    }

    public function isBlur(): bool
    {
        return $this->isBlur;
    }

    public function isLazy(): bool
    {
        return $this->isBlur();
    }

    public function isDebounced(): bool
    {
        return filled($this->debounce);
    }

    public function getDebounce(): int | string | null
    {
        return $this->debounce;
    }
}
