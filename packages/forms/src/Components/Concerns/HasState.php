<?php

namespace Filament\Forms\Components\Concerns;

use Filament\Forms\Components\Component;

trait HasState
{
    protected $afterStateHydrated = null;

    protected $afterStateUpdated = null;

    protected $beforeStateDehydrated = null;

    protected $defaultState = null;

    protected $dehydrateStateUsing = null;

    protected bool $hasDefaultState = false;

    protected $isDehydrated = true;

    protected ?string $statePath = null;

    public function afterStateHydrated(?callable $callback): static
    {
        $this->afterStateHydrated = $callback;

        return $this;
    }

    public function afterStateUpdated(?callable $callback): static
    {
        $this->afterStateUpdated = $callback;

        return $this;
    }

    public function beforeStateDehydrated(?callable $callback): static
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

    public function dehydrated(bool | callable $condition = true): static
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

    public function dehydrateStateUsing(?callable $callback): static
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

    public function state($state): static
    {
        $livewire = $this->getLivewire();

        data_set($livewire, $this->getStatePath(), $this->evaluate($state));

        return $this;
    }

    public function statePath(string $path): static
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
        return data_get($this->getLivewire(), $this->getStatePath());
    }

    public function getStatePath(bool $isAbsolute = true): string
    {
        $pathComponents = [];

        if ($isAbsolute && ($containerStatePath = $this->getContainer()->getStatePath())) {
            $pathComponents[] = $containerStatePath;
        }

        if (($statePath = $this->statePath) !== null) {
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

    protected function getGetCallback(): callable
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

    protected function getSetCallback(): callable
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
