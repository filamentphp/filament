<?php

namespace Filament\Schemas\Concerns;

use Closure;
use Exception;
use Filament\Infolists\Components\Entry;
use Filament\Support\Partials\SupportPartials;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HasState
{
    protected ?string $statePath = null;

    protected string $cachedAbsoluteStatePath;

    /**
     * @var array<string, mixed> | null
     */
    protected ?array $constantState = null;

    protected bool | Closure $shouldPartiallyRender = false;

    /**
     * @param  array<string, mixed> | null  $state
     */
    public function state(?array $state): static
    {
        $this->constantState($state);

        return $this;
    }

    /**
     * @param  array<string, mixed>  $state
     */
    public function rawState(array $state): static
    {
        $livewire = $this->getLivewire();

        if ($statePath = $this->getStatePath()) {
            data_set($livewire, $statePath, $state);
        } else {
            foreach ($state as $key => $value) {
                data_set($livewire, $key, $value);
            }
        }

        return $this;
    }

    /**
     * @param  array<string, mixed>  $state
     */
    public function partialRawState(array $state): static
    {
        $livewire = $this->getLivewire();

        if ($statePath = $this->getStatePath()) {
            foreach ($state as $key => $value) {
                data_set($livewire, "{$statePath}.{$key}", $value);
            }
        } else {
            foreach ($state as $key => $value) {
                data_set($livewire, $key, $value);
            }
        }

        return $this;
    }

    /**
     * @param  array<string, mixed> | null  $state
     */
    public function constantState(?array $state): static
    {
        $this->constantState = $state;

        return $this;
    }

    public function partiallyRender(bool | Closure $condition = true): static
    {
        $this->shouldPartiallyRender = $condition;

        return $this;
    }

    public function callAfterStateHydrated(): void
    {
        foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
            $component->callAfterStateHydrated();

            foreach ($component->getChildSchemas(withHidden: true) as $container) {
                $container->callAfterStateHydrated();
            }
        }
    }

    public function callAfterStateUpdated(string $path): bool
    {
        try {
            foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
                if ($component->getStatePath() === $path) {
                    $component->callAfterStateUpdated();

                    return true;
                }

                if (str($path)->startsWith("{$component->getStatePath()}.")) {
                    $component->callAfterStateUpdated();
                }

                foreach ($component->getChildSchemas() as $container) {
                    if ($container->callAfterStateUpdated($path)) {
                        return true;
                    }
                }
            }

            return false;
        } finally {
            if ($this->shouldPartiallyRender($path)) {
                app(SupportPartials::class)->renderPartial($this->getLivewire(), fn (): array => [
                    "schema.{$this->getKey()}" => $this->render(),
                ]);
            }
        }
    }

    public function callBeforeStateDehydrated(): void
    {
        foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
            if ($component->isHidden()) {
                continue;
            }

            $component->callBeforeStateDehydrated();

            foreach ($component->getChildSchemas() as $container) {
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
        foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
            if ($component->isHiddenAndNotDehydratedWhenHidden()) {
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
        foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
            if ($component->isHiddenAndNotDehydratedWhenHidden()) {
                continue;
            }

            if (! $component->isDehydrated()) {
                continue;
            }

            foreach ($component->getChildSchemas() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                $container->mutateDehydratedState($state);
            }

            if (filled($component->getStatePath(isAbsolute: false))) {
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
        foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
            if ($component->isHiddenAndNotDehydratedWhenHidden()) {
                continue;
            }

            foreach ($component->getChildSchemas() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                $container->mutateStateForValidation($state);
            }

            if (filled($component->getStatePath(isAbsolute: false))) {
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
            $this->rawState($state);
        }

        $this->hydrateState($hydratedDefaultState, $andCallHydrationHooks);

        if ($andFillStateWithNull) {
            $this->fillStateWithNull();
        }

        return $this;
    }

    /**
     * @param  array<string, mixed>  $state
     * @param  array<string>  $statePaths
     */
    public function fillPartially(array $state, array $statePaths, bool $andCallHydrationHooks = true, bool $andFillStateWithNull = true): static
    {
        $this->partialRawState(collect($state)->dot()->only($statePaths)->all());

        if ($schemaStatePath = $this->getStatePath()) {
            $statePaths = array_map(
                fn (string $statePath): string => "{$schemaStatePath}.{$statePath}",
                $statePaths,
            );
        }

        $this->hydrateStatePartially(
            $statePaths,
            $andCallHydrationHooks,
        );

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
        foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
            if ($component instanceof Entry) {
                continue;
            }

            $component->hydrateState($hydratedDefaultState, $andCallHydrationHooks);
        }
    }

    /**
     * @param  array<string>  $statePaths
     */
    public function hydrateStatePartially(array $statePaths, bool $andCallHydrationHooks = true): void
    {
        foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
            if ($component instanceof Entry) {
                continue;
            }

            $component->hydrateStatePartially($statePaths, $andCallHydrationHooks);
        }
    }

    public function fillStateWithNull(): void
    {
        foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
            $component->fillStateWithNull();
        }
    }

    public function statePath(?string $path): static
    {
        $this->statePath = $path;

        return $this;
    }

    /**
     * @return Model | array<string, mixed>
     */
    public function getConstantState(): Model | array
    {
        $state = $this->constantState ?? $this->getParentComponent()?->getContainer()->getConstantState();

        if ($state !== null) {
            return $state;
        }

        $record = $this->getRecord();

        if (! $record) {
            throw new Exception('Infolist has no [record()] or [state()] set.');
        }

        return $record;
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

    public function getStatePath(bool $isAbsolute = true): ?string
    {
        if (! $isAbsolute) {
            return $this->statePath;
        }

        if (isset($this->cachedAbsoluteStatePath)) {
            return $this->cachedAbsoluteStatePath;
        }

        $pathComponents = [];

        if ($parentComponentStatePath = $this->getParentComponent()?->getStatePath()) {
            $pathComponents[] = $parentComponentStatePath;
        }

        if (filled($statePath = $this->statePath)) {
            $pathComponents[] = $statePath;
        }

        return $this->cachedAbsoluteStatePath = implode('.', $pathComponents);
    }

    protected function flushCachedAbsoluteStatePath(): void
    {
        unset($this->cachedAbsoluteStatePath);
    }

    public function shouldPartiallyRender(?string $updatedStatePath = null): bool
    {
        if (! $this->evaluate($this->shouldPartiallyRender)) {
            return false;
        }

        if (blank($this->getKey())) {
            throw new Exception('You cannot partially render a schema without a [key()] or [statePath()] defined.');
        }

        return blank($updatedStatePath) || str($updatedStatePath)->startsWith("{$this->getStatePath()}.");
    }
}
