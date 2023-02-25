<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Livewire;

trait HasState
{
    protected ?Closure $afterStateHydrated = null;

    protected ?Closure $afterStateUpdated = null;

    protected ?Closure $beforeStateDehydrated = null;

    protected mixed $defaultState = null;

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
            $this->evaluate($callback, [
                'old' => $this->getOldState(),
            ]);
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

    public function default(mixed $state): static
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

    public function formatStateUsing(?Closure $callback): static
    {
        $this->afterStateHydrated(fn (Component $component) => $component->state($component->evaluate($callback)));

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getStateToDehydrate(): array
    {
        if ($callback = $this->dehydrateStateUsing) {
            return [$this->getStatePath() => $this->evaluate($callback)];
        }

        return [$this->getStatePath() => $this->getState()];
    }

    /**
     * @param  array<string, mixed>  $state
     */
    public function dehydrateState(array &$state): void
    {
        if (! $this->isDehydrated()) {
            if ($this->hasStatePath()) {
                Arr::forget($state, $this->getStatePath());
            }

            return;
        }

        if ($this->getStatePath(isAbsolute: false)) {
            foreach ($this->getStateToDehydrate() as $key => $value) {
                Arr::set($state, $key, $value);
            }
        }

        foreach ($this->getChildComponentContainers() as $container) {
            if ($container->isHidden()) {
                continue;
            }

            $container->dehydrateState($state);
        }
    }

    public function dehydrateStateUsing(?Closure $callback): static
    {
        $this->dehydrateStateUsing = $callback;

        return $this;
    }

    /**
     * @param  array<string, mixed> | null  $hydratedDefaultState
     */
    public function hydrateState(?array &$hydratedDefaultState): void
    {
        $this->hydrateDefaultState($hydratedDefaultState);

        foreach ($this->getChildComponentContainers(withHidden: true) as $container) {
            $container->hydrateState($hydratedDefaultState);
        }

        $this->callAfterStateHydrated();
    }

    public function fill(): void
    {
        $defaults = [];

        $this->hydrateDefaultState($defaults);
    }

    /**
     * @param  array<string, mixed> | null  $hydratedDefaultState
     */
    public function hydrateDefaultState(?array &$hydratedDefaultState): void
    {
        if ($hydratedDefaultState === null) {
            $this->loadStateFromRelationships();

            $state = $this->getState();

            // Hydrate all arrayable state objects as arrays by converting
            // them to collections, then using `toArray()`.
            if (is_array($state) || $state instanceof Arrayable) {
                $this->state(collect($state)->toArray());
            }

            return;
        }

        $statePath = $this->getStatePath();

        if (Arr::has($hydratedDefaultState, $statePath)) {
            return;
        }

        if (! $this->hasDefaultState()) {
            $this->state(null);

            return;
        }

        $defaultState = $this->getDefaultState();

        $this->state($this->getDefaultState());

        Arr::set($hydratedDefaultState, $statePath, $defaultState);
    }

    public function fillStateWithNull(): void
    {
        if (! Arr::has((array) $this->getLivewire(), $this->getStatePath())) {
            $this->state(null);
        }

        foreach ($this->getChildComponentContainers(withHidden: true) as $container) {
            $container->fillStateWithNull();
        }
    }

    public function mutateDehydratedState(mixed $state): mixed
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

    public function state(mixed $state): static
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

    public function getDefaultState(): mixed
    {
        return $this->evaluate($this->defaultState);
    }

    public function getState(): mixed
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

    public function getOldState(): mixed
    {
        if (! Livewire::isLivewireRequest()) {
            return null;
        }

        $state = request('serverMemo.data.' . $this->getStatePath());

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

        if ($this->hasStatePath()) {
            $pathComponents[] = $this->statePath;
        }

        return implode('.', $pathComponents);
    }

    public function hasStatePath(): bool
    {
        return filled($this->statePath);
    }

    protected function hasDefaultState(): bool
    {
        return $this->hasDefaultState;
    }

    public function isDehydrated(): bool
    {
        return (bool) $this->evaluate($this->isDehydrated);
    }

    public function getGetCallback(): Get
    {
        return new Get($this);
    }

    public function getSetCallback(): Set
    {
        return new Set($this);
    }

    public function generateRelativeStatePath(string | Component $path, bool $isAbsolute = false): string
    {
        if ($path instanceof Component) {
            return $path->getStatePath();
        }

        if ($isAbsolute) {
            return $path;
        }

        $containerPath = $this->getContainer()->getStatePath();

        while (str($path)->startsWith('../')) {
            $containerPath = Str::contains($containerPath, '.') ?
                (string) str($containerPath)->beforeLast('.') :
                null;

            $path = (string) str($path)->after('../');
        }

        if (blank($containerPath)) {
            return $path;
        }

        return "{$containerPath}.{$path}";
    }
}
