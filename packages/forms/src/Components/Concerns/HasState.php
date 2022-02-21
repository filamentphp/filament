<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Component;

trait HasState
{
    protected ?Closure $afterStateHydrated = null;

    protected ?Closure $afterStateUpdated = null;

    protected ?Closure $beforeStateDehydrated = null;

    protected $defaultState = null;

    protected ?Closure $dehydrateStateUsing = null;

    protected ?Closure $mutateDehydratedStateUsing = null;

    protected bool $hasDefaultState = false;

    protected bool | Closure $isDehydrated = true;

    protected ?string $statePath = null;

    public function afterStateHydrated(?Closure $callback): static
    {
        $this->afterStateHydrated = $callback;

        return $this;
    }

    public function afterStateUpdated(?Closure $callback): static
    {
        $this->afterStateUpdated = $callback;

        return $this;
    }

    public function beforeStateDehydrated(?Closure $callback): static
    {
        $this->beforeStateDehydrated = $callback;

        return $this;
    }

    public function callAfterStateHydrated(): static
    {
        if ($callback = $this->afterStateHydrated) {
            $this->evaluate($callback);
        }

        return $this;
    }

    public function callAfterStateUpdated(): static
    {
        if ($callback = $this->afterStateUpdated) {
            $this->evaluate($callback);
        }

        return $this;
    }

    public function callBeforeStateDehydrated(): static
    {
        if ($callback = $this->beforeStateDehydrated) {
            $this->evaluate($callback);
        }

        return $this;
    }

    public function default($state): static
    {
        $this->defaultState = $state;
        $this->hasDefaultState = true;

        return $this;
    }

    public function dehydrated(bool | Closure $condition = true): static
    {
        $this->isDehydrated = $condition;

        return $this;
    }

    public function dehydrateState()
    {
        if ($callback = $this->dehydrateStateUsing) {
            return $this->evaluate($callback);
        }

        return $this->getState();
    }

    public function dehydrateStateUsing(?Closure $callback): static
    {
        $this->dehydrateStateUsing = $callback;

        return $this;
    }

    public function hydrateDefaultState(): static
    {
        if (! $this->hasDefaultState()) {
            return $this;
        }

        $this->state($this->getDefaultState());

        return $this;
    }

    public function fillMissingStateWithNull(): static
    {
        $livewire = $this->getLivewire();

        data_fill($livewire, $this->getStatePath(), null);

        return $this;
    }

    public function mutateDehydratedState($state)
    {
        return $this->evaluate(
            $this->mutateDehydratedStateUsing,
            ['state' => $state],
        );
    }

    public function mutatesDehydratedState(): bool
    {
        return $this->mutateDehydratedStateUsing instanceof Closure;
    }

    public function mutateDehydratedStateUsing(?Closure $callback): static
    {
        $this->mutateDehydratedStateUsing = $callback;

        return $this;
    }

    public function state($state): static
    {
        $livewire = $this->getLivewire();

        data_set($livewire, $this->getStatePath(), $this->evaluate($state));

        return $this;
    }

    public function statePath(?string $path): static
    {
        $this->statePath = $path;

        return $this;
    }

    public function getDefaultState()
    {
        return $this->evaluate($this->defaultState);
    }

    public function getState()
    {
        $state = data_get($this->getLivewire(), $this->getStatePath());

        if (is_array($state)) {
            return $state;
        }

        if (blank($state)) {
            return null;
        }

        return $state;
    }

    public function getStatePath(bool $isAbsolute = true): string
    {
        $pathComponents = [];

        if ($isAbsolute && ($containerStatePath = $this->getContainer()->getStatePath())) {
            $pathComponents[] = $containerStatePath;
        }

        if (filled($statePath = $this->statePath)) {
            $pathComponents[] = $statePath;
        }

        return implode('.', $pathComponents);
    }

    protected function hasDefaultState(): bool
    {
        return $this->hasDefaultState;
    }

    public function isDehydrated(): bool
    {
        return (bool) $this->evaluate($this->isDehydrated);
    }

    protected function getGetCallback(): Closure
    {
        return function (Component | string $path, bool $isAbsolute = false) {
            if ($path instanceof Component) {
                $path = $path->getStatePath();
            } elseif (
                (! $isAbsolute) &&
                ($containerPath = $this->getContainer()->getStatePath())
            ) {
                $path = "{$containerPath}.{$path}";
            }

            $livewire = $this->getLivewire();

            return data_get($livewire, $path);
        };
    }

    protected function getSetCallback(): Closure
    {
        return function (string | Component $path, $state, bool $isAbsolute = false) {
            if ($path instanceof Component) {
                $path = $path->getStatePath();
            } elseif (
                (! $isAbsolute) &&
                ($containerPath = $this->getContainer()->getStatePath())
            ) {
                $path = "{$containerPath}.{$path}";
            }

            $livewire = $this->getLivewire();
            data_set($livewire, $path, $this->evaluate($state));

            return $state;
        };
    }
}
