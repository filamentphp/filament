<?php

namespace Filament\Schemas\Concerns;

use Closure;
use Filament\Infolists\Components\Entry;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Support\Livewire\Partials\PartialsComponentHook;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use LogicException;
use WeakMap;

trait HasState
{
    protected ?string $statePath = null;

    protected string $cachedAbsoluteStatePath;

    protected bool $shouldLoadStateFromRelationshipsWhenHydratingPartially = true;

    /**
     * @var array<string, mixed> | object | null
     */
    protected array | object | null $constantState = null;

    protected bool | Closure $shouldPartiallyRender = false;

    /**
     * @var array<string, bool>
     */
    protected array $dehydratedComponentsCache = [];

    /**
     * @var WeakMap<Component, bool> | null
     */
    protected ?WeakMap $cachedComponentDehydrationDecisions = null;

    /**
     * @var WeakMap<Component, array<Schema>> | null
     */
    protected ?WeakMap $cachedChildSchemasForStatePathSearch = null;

    /**
     * @param  array<string, mixed> | object | null  $state
     */
    public function state(array | object | null $state): static
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
     * @param  array<string, mixed> | object | null  $state
     */
    public function constantState(array | object | null $state): static
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

            foreach ($component->getChildSchemas(withHidden: true) as $childSchema) {
                $childSchema->callAfterStateHydrated();
            }
        }
    }

    public function callAfterStateUpdated(string $path): bool
    {
        try {
            foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
                $componentStatePath = $component->getStatePath();

                if ($componentStatePath === $path) {
                    $component->callAfterStateUpdated(shouldBubbleToParents: false);

                    return true;
                }

                if (str($path)->startsWith("{$componentStatePath}.")) {
                    $component->callAfterStateUpdated(shouldBubbleToParents: false);
                }

                foreach ($component->getChildSchemas() as $childSchema) {
                    if ($childSchema->callAfterStateUpdated($path)) {
                        return true;
                    }
                }
            }

            return false;
        } finally {
            if ($this->shouldPartiallyRender($path)) {
                app(PartialsComponentHook::class)->renderPartial($this->getLivewire(), fn (): array => [
                    "schema.{$this->getKey()}" => $this->toHtml(...),
                ]);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $state
     */
    public function callBeforeStateDehydrated(array &$state = []): void
    {
        foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
            if ($component->isHiddenAndNotDehydratedWhenHidden()) {
                continue;
            }

            $component->callBeforeStateDehydrated($state);

            foreach ($component->getChildSchemas(withHidden: true) as $childSchema) {
                if ($childSchema->isHiddenAndNotDehydratedWhenHidden()) {
                    continue;
                }

                $childSchema->callBeforeStateDehydrated($state);
            }
        }
    }

    /**
     * Only components on the branch of the `statePath` are visited: a
     * component whose own `statePath` is not a prefix of it cannot contain it.
     *
     * @param  array<string>  $ancestorStatePaths
     * @param  WeakMap<Component, bool>  $componentDehydrationDecisions
     */
    protected function searchDehydratedComponent(string $statePath, array $ancestorStatePaths, WeakMap $componentDehydrationDecisions): bool
    {
        $schemaStatePath = $this->getStatePath();

        if (filled($schemaStatePath) && ($schemaStatePath !== $statePath) && (! str_starts_with($statePath, "{$schemaStatePath}."))) {
            return false;
        }

        $index = $this->getComponentsIndexedByStatePath();

        foreach ($index['withStatePath'][$statePath] ?? [] as $component) {
            if ($componentDehydrationDecisions[$component] ??= $component->isDehydrated()) {
                return true;
            }
        }

        $branchComponents = $index['withoutStatePath'];

        foreach ($ancestorStatePaths as $ancestorStatePath) {
            array_push($branchComponents, ...($index['withStatePath'][$ancestorStatePath] ?? []));
        }

        foreach ($branchComponents as $component) {
            if (! ($componentDehydrationDecisions[$component] ??= $component->isDehydrated())) {
                continue;
            }

            foreach ($this->getChildSchemasForStatePathSearch($component) as $childSchema) {
                if ($childSchema->searchDehydratedComponent($statePath, $ancestorStatePaths, $componentDehydrationDecisions)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return array{withStatePath: array<string, array<Component>>, withoutStatePath: array<Component>}
     */
    protected function getComponentsIndexedByStatePath(): array
    {
        if ($this->cachedComponentsIndexedByStatePath !== null) {
            return $this->cachedComponentsIndexedByStatePath;
        }

        $index = ['withStatePath' => [], 'withoutStatePath' => []];

        foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
            if ($component->hasStatePath()) {
                $index['withStatePath'][$component->getStatePath()][] = $component;
            } else {
                $index['withoutStatePath'][] = $component;
            }
        }

        return $this->cachedComponentsIndexedByStatePath = $index;
    }

    public function hasDehydratedComponent(string $statePath): bool
    {
        if (array_key_exists($statePath, $this->dehydratedComponentsCache)) {
            return $this->dehydratedComponentsCache[$statePath];
        }

        $ancestorStatePaths = [];
        $ancestorStatePath = $statePath;

        while (($separatorPosition = strrpos($ancestorStatePath, '.')) !== false) {
            $ancestorStatePath = substr($ancestorStatePath, 0, $separatorPosition);
            $ancestorStatePaths[] = $ancestorStatePath;
        }

        return $this->dehydratedComponentsCache[$statePath] = $this->searchDehydratedComponent(
            $statePath,
            $ancestorStatePaths,
            $this->cachedComponentDehydrationDecisions ??= new WeakMap,
        );
    }

    /**
     * @internal Do not use this method outside the internals of Filament. It is subject to breaking changes in minor and patch releases.
     *
     * @return array<Schema>
     */
    public function getChildSchemasForStatePathSearch(Component $component): array
    {
        $rootContainer = $this->getParentComponent()?->getRootContainer() ?? $this;

        if ($rootContainer->cachedChildSchemasForStatePathSearch === null) {
            return $component->getChildSchemas(withHidden: true);
        }

        return $rootContainer->cachedChildSchemasForStatePathSearch[$component] ??= $component->getChildSchemas(withHidden: true);
    }

    /**
     * @param  array<string, mixed>  $state
     * @return array<string, mixed>
     */
    public function dehydrateState(array &$state = [], bool $isDehydrated = true): array
    {
        $rootContainer = $this->getParentComponent()?->getRootContainer() ?? $this;
        $isStartingDehydration = $rootContainer->cachedChildSchemasForStatePathSearch === null;

        if ($isStartingDehydration) {
            $rootContainer->cachedChildSchemasForStatePathSearch = new WeakMap;
            $rootContainer->dehydratedComponentsCache = [];
            $rootContainer->cachedComponentDehydrationDecisions = null;
        }

        try {
            foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
                $component->dehydrateState($state, $isDehydrated);
            }

            return $state;
        } finally {
            if ($isStartingDehydration) {
                $rootContainer->cachedChildSchemasForStatePathSearch = null;
                $rootContainer->dehydratedComponentsCache = [];
                $rootContainer->cachedComponentDehydrationDecisions = null;
            }
        }
    }

    /**
     * @param  array<string, mixed>  $state
     * @return array<string, mixed>
     */
    public function mutateDehydratedState(array &$state = []): array
    {
        foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
            if (! $component->isDehydrated()) {
                continue;
            }

            foreach ($component->getChildSchemas(withHidden: true) as $childSchema) {
                if ($childSchema->isHiddenAndNotDehydratedWhenHidden()) {
                    continue;
                }

                $childSchema->mutateDehydratedState($state);
            }

            if (filled($component->getStatePath(isAbsolute: false))) {
                if (! $component->mutatesDehydratedState()) {
                    continue;
                }

                $componentStatePath = $component->getStatePath();

                data_set(
                    $state,
                    $componentStatePath,
                    $component->mutateDehydratedState(data_get($state, $componentStatePath)),
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

            foreach ($component->getChildSchemas(withHidden: true) as $childSchema) {
                if ($childSchema->isHiddenAndNotDehydratedWhenHidden()) {
                    continue;
                }

                $childSchema->mutateStateForValidation($state);
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
     * Takes the raw Livewire state and prunes it to only contain keys
     * present in the template array. This preserves the sparse structure
     * of validated data while using clean (unmutated) values from Livewire.
     *
     * @param  array<string, mixed>  $source
     * @param  array<string, mixed>  $template
     * @return array<string, mixed>
     */
    protected function pruneStateToMatchKeys(array $source, array $template): array
    {
        $result = [];

        foreach ($template as $key => $templateValue) {
            if (! array_key_exists($key, $source)) {
                $result[$key] = $templateValue;

                continue;
            }

            if (is_array($templateValue) && is_array($source[$key])) {
                $result[$key] = $this->pruneStateToMatchKeys($source[$key], $templateValue);
            } else {
                $result[$key] = $source[$key];
            }
        }

        return $result;
    }

    /**
     * @param  array<string, mixed> | null  $state
     * @param  array<string, true>  $appliedStateCastPaths
     */
    public function fill(?array $state = null, bool $shouldCallHydrationHooks = true, bool $shouldFillStateWithNull = true, bool $shouldApplyStateCasts = true, array &$appliedStateCastPaths = []): static
    {
        $hydratedDefaultState = null;

        if ($state === null) {
            $hydratedDefaultState = [];
        } else {
            $this->rawState($state);
        }

        $this->hydrateState($hydratedDefaultState, $shouldCallHydrationHooks, $shouldApplyStateCasts, $appliedStateCastPaths);

        if ($shouldFillStateWithNull) {
            $this->fillStateWithNull();
        }

        return $this;
    }

    /**
     * @param  array<string, mixed>  $state
     * @param  array<string>  $statePaths
     */
    public function fillPartially(array $state, array $statePaths, bool $shouldCallHydrationHooks = true, bool $shouldFillStateWithNull = true): static
    {
        $partialState = [];

        foreach ($statePaths as $statePath) {
            if (array_key_exists($statePath, $state)) {
                $partialState[$statePath] = $state[$statePath];

                continue;
            }

            if (Arr::has($state, $statePath)) {
                $partialState[$statePath] = data_get($state, $statePath);
            }
        }

        $this->partialRawState($partialState);

        if ($schemaStatePath = $this->getStatePath()) {
            $statePaths = array_map(
                fn (string $statePath): string => "{$schemaStatePath}.{$statePath}",
                $statePaths,
            );
        }

        $this->hydrateStatePartially(
            $statePaths,
            $shouldCallHydrationHooks,
        );

        if ($shouldFillStateWithNull) {
            $this->fillStateWithNull();
        }

        return $this;
    }

    /**
     * @internal Do not use this method outside the internals of Filament. It is subject to breaking changes in minor and patch releases.
     *
     * @param  array<string, mixed>  $state
     * @param  array<string>  $statePaths
     */
    public function fillPartiallyWithoutLoadingStateFromRelationships(array $state, array $statePaths, bool $shouldCallHydrationHooks = true, bool $shouldFillStateWithNull = true): static
    {
        $shouldLoadStateFromRelationships = $this->shouldLoadStateFromRelationshipsWhenHydratingPartially;

        $this->shouldLoadStateFromRelationshipsWhenHydratingPartially = false;

        try {
            return $this->fillPartially($state, $statePaths, $shouldCallHydrationHooks, $shouldFillStateWithNull);
        } finally {
            $this->shouldLoadStateFromRelationshipsWhenHydratingPartially = $shouldLoadStateFromRelationships;
        }
    }

    /**
     * @param  array<string, mixed> | null  $hydratedDefaultState
     * @param  array<string, true>  $appliedStateCastPaths
     */
    public function hydrateState(?array &$hydratedDefaultState, bool $shouldCallHydrationHooks = true, bool $shouldApplyStateCasts = true, array &$appliedStateCastPaths = []): void
    {
        foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
            if ($component instanceof Entry) {
                continue;
            }

            $component->hydrateState($hydratedDefaultState, $shouldCallHydrationHooks, $shouldApplyStateCasts, $appliedStateCastPaths);
        }
    }

    /**
     * @param  array<string>  $statePaths
     */
    public function hydrateStatePartially(array $statePaths, bool $shouldCallHydrationHooks = true): void
    {
        foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
            if ($component instanceof Entry) {
                continue;
            }

            $component->hydrateStatePartially($statePaths, $shouldCallHydrationHooks);
        }
    }

    /**
     * @internal Do not use this method outside the internals of Filament. It is subject to breaking changes in minor and patch releases.
     */
    public function shouldLoadStateFromRelationshipsWhenHydratingPartially(): bool
    {
        return $this->shouldLoadStateFromRelationshipsWhenHydratingPartially &&
            ($this->getParentComponent()?->getContainer()->shouldLoadStateFromRelationshipsWhenHydratingPartially() ?? true);
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
     * @internal Do not use this method outside the internals of Filament. It is subject to breaking changes in minor and patch releases.
     *
     * @return array<string, mixed> | object
     */
    public function getConstantState(): array | object
    {
        return $this->evaluate($this->constantState)
            ?? $this->getRecord(withParentComponentRecord: false)
            ?? $this->getParentComponent()?->getContainer()->getConstantState()
            ?? $this->getRecord()
            ?? [];
    }

    /**
     * @internal Do not use this method outside the internals of Filament. It is subject to breaking changes in minor and patch releases.
     */
    public function getConstantStatePath(): ?string
    {
        if ($this->constantState !== null) {
            return $this->getStatePath();
        }

        if ($this->getRecord(withParentComponentRecord: false) !== null) {
            return $this->getStatePath();
        }

        if ($this->getParentComponent()?->getContainer()->getConstantState() !== null) {
            return $this->getParentComponent()->getContainer()->getConstantStatePath();
        }

        return $this->getParentComponent()?->getRecordConstantStatePath();
    }

    /**
     * @return array<string, mixed>
     */
    public function getState(bool $shouldCallHooksBefore = true, ?Closure $afterValidate = null): array
    {
        return Component::withVisibilityCache(function () use ($shouldCallHooksBefore, $afterValidate): array {
            $state = $this->validate();

            // `validate()` returns data that went through `prepareForValidation()`,
            // which applies `mutateStateForValidation()` mutations. Those mutations
            // should only affect validation rules, not the dehydrated output. Replace
            // the mutated values with clean Livewire data, preserving the validated
            // array's sparse key structure.
            $statePath = $this->getStatePath();
            $livewire = $this->getLivewire();

            if (filled($statePath)) {
                $rawState = data_get($livewire, $statePath);

                if (is_array($rawState)) {
                    $validatedFormData = data_get($state, $statePath);

                    if (is_array($validatedFormData)) {
                        data_set($state, $statePath, $this->pruneStateToMatchKeys($rawState, $validatedFormData));
                    }
                }
            } else {
                $rawState = [];

                foreach (array_keys($state) as $key) {
                    $rawState[$key] = data_get($livewire, $key);
                }

                $state = $this->pruneStateToMatchKeys($rawState, $state);
            }

            if ($shouldCallHooksBefore) {
                $this->callBeforeStateDehydrated($state);

                $afterValidate || $this->saveRelationships();
                $afterValidate || $this->loadStateFromRelationships(shouldHydrate: true);
            }

            $this->dehydrateState($state);
            $this->mutateDehydratedState($state);

            if ($statePath = $this->getStatePath()) {
                $state = data_get($state, $statePath) ?? [];
            }

            if ($afterValidate) {
                value($afterValidate, $state);

                $shouldCallHooksBefore && $this->saveRelationships();
                $shouldCallHooksBefore && $this->loadStateFromRelationships(shouldHydrate: true);
            }

            return $state;
        });
    }

    /**
     * @internal Do not use this method outside the internals of Filament. It is subject to breaking changes in minor and patch releases.
     *
     * @return array<string, mixed>
     */
    public function getStateSnapshot(): array
    {
        return Component::withVisibilityCache(function (): array {
            $statePath = $this->getStatePath();

            if (filled($statePath)) {
                $state = [];
                data_set($state, $statePath, $this->getRawState());
            } else {
                $state = $this->getRawState();
            }

            $this->dehydrateState($state);
            $this->mutateDehydratedState($state);

            if ($statePath) {
                $state = data_get($state, $statePath) ?? [];
            }

            return $state;
        });
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
            // Security: Strip characters that could break out of a quoted HTML attribute or a JS string, so every downstream sink that embeds the state path raw is safe without per-sink escaping. Client-influenced repeater item keys flow in here, and legitimate state paths never contain these characters.
            return ($this->statePath === null) ? null : preg_replace('/[<>"\'`\x00-\x1F\x7F]/', '', $this->statePath);
        }

        if (isset($this->cachedAbsoluteStatePath)) {
            return $this->cachedAbsoluteStatePath;
        }

        $pathComponents = [];

        if ($parentComponentStatePath = $this->getParentComponent()?->getStatePath()) {
            $pathComponents[] = $parentComponentStatePath;
        }

        if (filled($this->statePath)) {
            $pathComponents[] = $this->getStatePath(isAbsolute: false);
        }

        return $this->cachedAbsoluteStatePath = implode('.', $pathComponents);
    }

    public function flushCachedAbsoluteStatePath(): void
    {
        /** @phpstan-ignore unset.possiblyHookedProperty */
        unset($this->cachedAbsoluteStatePath);
        $this->cachedComponentsIndexedByStatePath = null;
    }

    public function flushCachedAbsoluteStatePaths(): void
    {
        $this->flushCachedAbsoluteStatePath();

        foreach ($this->getComponents(withActions: false, withHidden: true) as $component) {
            $component->flushCachedAbsoluteStatePath();

            foreach ($component->getChildSchemas(withHidden: true) as $childSchema) {
                $childSchema->flushCachedAbsoluteStatePaths();
            }
        }
    }

    public function shouldPartiallyRender(?string $updatedStatePath = null): bool
    {
        if (! $this->evaluate($this->shouldPartiallyRender)) {
            return false;
        }

        if (blank($this->getKey())) {
            throw new LogicException('You cannot partially render a schema without a [key()] or [statePath()] defined.');
        }

        return blank($updatedStatePath) || str($updatedStatePath)->startsWith("{$this->getStatePath()}.");
    }
}
