<?php

namespace Filament\Forms\Concerns;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

trait HasState
{
    protected ?string $statePath = null;

    protected string $cachedAbsoluteStatePath;

    public function callAfterStateHydrated(): void
    {
        foreach ($this->getComponents(withHidden: true) as $component) {
            $component->callAfterStateHydrated();

            foreach ($component->getChildComponentContainers(withHidden: true) as $container) {
                $container->callAfterStateHydrated();
            }
        }
    }

    public function callAfterStateUpdated(string $path): bool
    {
        foreach ($this->getComponents(withHidden: true) as $component) {
            if ($component->getStatePath() === $path) {
                $component->callAfterStateUpdated();

                return true;
            }

            if (str($path)->startsWith("{$component->getStatePath()}.")) {
                $component->callAfterStateUpdated();
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->callAfterStateUpdated($path)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function callBeforeStateDehydrated(): void
    {
        foreach ($this->getComponents(withHidden: true) as $component) {
            if ($component->isHidden()) {
                continue;
            }

            $component->callBeforeStateDehydrated();

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                $container->callBeforeStateDehydrated();
            }
        }
    }

    /**
     * @param  array<string, mixed>  $state
     * @return array<string, mixed>
     */
    public function dehydrateState(array &$state = [], bool $isDehydrated = true): array
    {
        foreach ($this->getComponents(withHidden: true) as $component) {
            if ($component->isHiddenAndNotDehydrated()) {
                continue;
            }

            $component->dehydrateState($state, $isDehydrated);
        }

        return $state;
    }

    /**
     * @param  array<string, mixed>  $state
     * @return array<string, mixed>
     */
    public function mutateDehydratedState(array &$state = []): array
    {
        foreach ($this->getComponents(withHidden: true) as $component) {
            if ($component->isHiddenAndNotDehydrated()) {
                continue;
            }

            if (! $component->isDehydrated()) {
                continue;
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                $container->mutateDehydratedState($state);
            }

            if ($component->getStatePath(isAbsolute: false)) {
                if (! $component->mutatesDehydratedState()) {
                    continue;
                }

                $componentStatePath = $component->getStatePath();

                data_set(
                    $state,
                    $componentStatePath,
                    $component->mutateDehydratedState(
                        data_get($state, $componentStatePath),
                    ),
                );
            }
        }

        return $state;
    }

    /**
     * @param  array<string, mixed>  $state
     * @return array<string, mixed>
     */
    public function mutateStateForValidation(array &$state = []): array
    {
        foreach ($this->getComponents(withHidden: true) as $component) {
            if ($component->isHiddenAndNotDehydrated()) {
                continue;
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                $container->mutateStateForValidation($state);
            }

            if ($component->getStatePath(isAbsolute: false)) {
                if (! $component->mutatesStateForValidation()) {
                    continue;
                }

                $componentStatePath = $component->getStatePath();

                data_set(
                    $state,
                    $componentStatePath,
                    $component->mutateStateForValidation(
                        data_get($state, $componentStatePath),
                    ),
                );
            }
        }

        return $state;
    }

    /**
     * @param  array<string, mixed> | null  $state
     */
    public function fill(?array $state = null, bool $andCallHydrationHooks = true, bool $andFillStateWithNull = true): static
    {
        $hydratedDefaultState = null;

        if ($state === null) {
            $hydratedDefaultState = [];
        } else {
            $livewire = $this->getLivewire();

            if ($statePath = $this->getStatePath()) {
                data_set($livewire, $statePath, $state);
            } else {
                foreach ($state as $key => $value) {
                    data_set($livewire, $key, $value);
                }
            }
        }

        $this->hydrateState($hydratedDefaultState, $andCallHydrationHooks);

        if ($andFillStateWithNull) {
            $this->fillStateWithNull();
        }

        return $this;
    }

    /**
     * @param  array<string, mixed> | null  $hydratedDefaultState
     */
    public function hydrateState(?array &$hydratedDefaultState, bool $andCallHydrationHooks = true): void
    {
        foreach ($this->getComponents(withHidden: true) as $component) {
            $component->hydrateState($hydratedDefaultState, $andCallHydrationHooks);
        }
    }

    public function fillStateWithNull(): void
    {
        foreach ($this->getComponents(withHidden: true) as $component) {
            $component->fillStateWithNull();
        }
    }

    public function statePath(?string $path): static
    {
        $this->statePath = $path;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getState(bool $shouldCallHooksBefore = true, ?Closure $afterValidate = null): array
    {
        $state = $this->validate();

        if ($shouldCallHooksBefore) {
            $this->callBeforeStateDehydrated();

            $afterValidate || $this->saveRelationships();
            $afterValidate || $this->loadStateFromRelationships(andHydrate: true);
        }

        $this->dehydrateState($state);
        $this->mutateDehydratedState($state);

        if ($statePath = $this->getStatePath()) {
            $state = data_get($state, $statePath) ?? [];
        }

        if ($afterValidate) {
            value($afterValidate, $state);

            $shouldCallHooksBefore && $this->saveRelationships();
            $shouldCallHooksBefore && $this->loadStateFromRelationships(andHydrate: true);
        }

        return $state;
    }

    /**
     * @return array<string, mixed> | Arrayable
     */
    public function getRawState(): array | Arrayable
    {
        return data_get($this->getLivewire(), $this->getStatePath()) ?? [];
    }

    /**
     * @param  array<string>  $keys
     * @return array<string, mixed>
     */
    public function getStateOnly(array $keys, bool $shouldCallHooksBefore = true): array
    {
        return Arr::only($this->getState($shouldCallHooksBefore), $keys);
    }

    /**
     * @param  array<string>  $keys
     * @return array<string, mixed>
     */
    public function getStateExcept(array $keys, bool $shouldCallHooksBefore = true): array
    {
        return Arr::except($this->getState($shouldCallHooksBefore), $keys);
    }

    public function getStatePath(bool $isAbsolute = true): string
    {
        if (! $isAbsolute) {
            return $this->statePath ?? '';
        }

        if (isset($this->cachedAbsoluteStatePath)) {
            return $this->cachedAbsoluteStatePath;
        }

        $pathComponents = [];

        if ($parentComponentStatePath = $this->getParentComponent()?->getStatePath()) {
            $pathComponents[] = $parentComponentStatePath;
        }

        if (($statePath = $this->statePath) !== null) {
            $pathComponents[] = $statePath;
        }

        return $this->cachedAbsoluteStatePath = implode('.', $pathComponents);
    }

    protected function flushCachedAbsoluteStatePath(): void
    {
        unset($this->cachedAbsoluteStatePath);
    }
}
