<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Exception;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Filament\Infolists\Components\Entry;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Livewire;

use function Livewire\store;

trait HasState
{
    use CanGetStateFromRelationships;

    protected ?Closure $afterStateHydrated = null;

    /**
     * @var array<Closure>
     */
    protected array $afterStateUpdated = [];

    /**
     * @var array<string | Closure>
     */
    protected array $afterStateUpdatedJs = [];

    protected ?Closure $beforeStateDehydrated = null;

    protected bool $shouldUpdateValidatedStateAfterBeforeStateDehydratedRuns = false;

    protected mixed $defaultState = null;

    protected ?Closure $dehydrateStateUsing = null;

    protected ?Closure $mutateDehydratedStateUsing = null;

    protected ?Closure $mutateStateForValidationUsing = null;

    protected bool $hasDefaultState = false;

    protected bool | Closure $isDehydrated = true;

    protected bool | Closure $isDehydratedWhenHidden = false;

    protected bool | Closure $isValidatedWhenNotDehydrated = true;

    protected ?string $statePath = null;

    protected string $cachedAbsoluteStatePath;

    protected mixed $getConstantStateUsing = null;

    protected bool $hasConstantState = false;

    protected string | Closure | null $separator = null;

    protected bool | Closure $isDistinctList = false;

    /**
     * @var array<StateCast | Closure>
     */
    protected array $stateCasts = [];

    public function stateCast(StateCast | Closure $cast): static
    {
        $this->stateCasts[] = $cast;

        return $this;
    }

    /**
     * @return array<StateCast>
     */
    public function getStateCasts(): array
    {
        $casts = $this->getDefaultStateCasts();

        foreach ($this->stateCasts as $cast) {
            $casts[] = $this->evaluate($cast);
        }

        return $casts;
    }

    /**
     * @return array<StateCast>
     */
    public function getDefaultStateCasts(): array
    {
        return [];
    }

    public function afterStateHydrated(?Closure $callback): static
    {
        $this->afterStateHydrated = $callback;

        return $this;
    }

    public function clearAfterStateUpdatedHooks(): static
    {
        $this->afterStateUpdated = [];

        return $this;
    }

    public function afterStateUpdated(?Closure $callback): static
    {
        if (blank($callback)) {
            $this->afterStateUpdated = [];

            return $this;
        }

        $this->afterStateUpdated[] = $callback;

        return $this;
    }

    public function afterStateUpdatedJs(string | Closure | null $js): static
    {
        if (blank($js)) {
            $this->afterStateUpdatedJs = [];

            return $this;
        }

        $this->afterStateUpdatedJs[] = $js;

        return $this;
    }

    public function beforeStateDehydrated(?Closure $callback, bool $shouldUpdateValidatedStateAfter = false): static
    {
        $this->beforeStateDehydrated = $callback;
        $this->shouldUpdateValidatedStateAfterBeforeStateDehydratedRuns = $shouldUpdateValidatedStateAfter;

        return $this;
    }

    public function callAfterStateHydrated(): static
    {
        if ($callback = $this->afterStateHydrated) {
            $this->evaluate($callback);
        }

        return $this;
    }

    public function callAfterStateUpdated(bool $shouldBubbleToParents = true): static
    {
        $this->callAfterStateUpdatedHooks();

        if ($this->isPartiallyRenderedAfterStateUpdated()) {
            $this->partiallyRender();
        }

        if (filled($components = $this->getComponentsToPartiallyRenderAfterStateUpdated())) {
            foreach ($components as $key) {
                $this->getLivewire()->getSchemaComponent($this->resolveRelativeKey($key))->partiallyRender();
            }
        }

        if ($this->isRenderlessAfterStateUpdated()) {
            $this->skipRender();
        }

        if ($shouldBubbleToParents) {
            $this->getContainer()->getParentComponent()?->callAfterStateUpdated();
        }

        return $this;
    }

    protected function callAfterStateUpdatedHooks(): static
    {
        foreach ($this->afterStateUpdated as $callback) {
            $runId = spl_object_id($callback) . md5(json_encode($this->getState()));

            if (store($this)->has('executedAfterStateUpdatedCallbacks', iKey: $runId)) {
                continue;
            }

            $this->callAfterStateUpdatedHook($callback);

            store($this)->push('executedAfterStateUpdatedCallbacks', value: $runId, iKey: $runId);
        }

        return $this;
    }

    protected function callAfterStateUpdatedHook(Closure $hook): void
    {
        $this->evaluate($hook, [
            'old' => $this->getOldState(),
            'oldRaw' => $this->getOldRawState(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $state
     */
    public function callBeforeStateDehydrated(array &$state = []): static
    {
        if (! $this->beforeStateDehydrated) {
            return $this;
        }

        $this->evaluate($this->beforeStateDehydrated);

        if ($this->shouldUpdateValidatedStateAfterBeforeStateDehydratedRuns) {
            Arr::set($state, $this->getStatePath(), $this->getState()); /** @phpstan-ignore parameterByRef.type */
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

    public function dehydratedWhenHidden(bool | Closure $condition = true): static
    {
        $this->isDehydratedWhenHidden = $condition;

        return $this;
    }

    public function validatedWhenNotDehydrated(bool | Closure $condition = true): static
    {
        $this->isValidatedWhenNotDehydrated = $condition;

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
    public function getStateToDehydrate(mixed $state): array
    {
        foreach ($this->getStateCasts() as $stateCast) {
            $state = $stateCast->get($state);
        }

        if ($callback = $this->dehydrateStateUsing) {
            return [$this->getStatePath() => $this->evaluate($callback, [
                'state' => $state,
            ])];
        }

        return [$this->getStatePath() => $state];
    }

    /**
     * @param  array<string, mixed>  $state
     */
    public function dehydrateState(array &$state, bool $isDehydrated = true): void
    {
        if (! ($isDehydrated && $this->isDehydrated())) {
            if ($this->hasStatePath()) {
                $statePath = $this->getStatePath();

                if (! $this->getRootContainer()->hasDehydratedComponent($statePath)) {
                    Arr::forget($state, $statePath); /** @phpstan-ignore parameterByRef.type */

                    return;
                }

                return;
            }

            // If the component is not dehydrated, but it has child components,
            // we need to dehydrate the child component containers while
            // informing them that they are not dehydrated, so that their
            // child components get removed from the state.
            foreach ($this->getChildSchemas(withHidden: true) as $childSchema) {
                $childSchema->dehydrateState($state, isDehydrated: false);
            }

            return;
        }

        foreach ($this->getChildSchemas(withHidden: true) as $childSchema) {
            $childSchema->dehydrateState($state, $isDehydrated);
        }

        if ($this->hasStatePath()) {
            foreach ($this->getStateToDehydrate(Arr::get($state, $this->getStatePath())) as $key => $value) {
                Arr::set($state, $key, $value); /** @phpstan-ignore parameterByRef.type */
            }
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
    public function hydrateState(?array &$hydratedDefaultState, bool $shouldCallHydrationHooks = true): void
    {
        $this->hydrateDefaultState($hydratedDefaultState);

        if ($hydratedDefaultState === null) {
            $this->loadStateFromRelationships();

            $rawState = $this->getRawState();

            // Hydrate all arrayable state objects as arrays by converting
            // them to collections, then using `toArray()`.
            if (is_array($rawState) || $rawState instanceof Arrayable) {
                $rawState = collect($rawState)->toArray();

                $this->rawState($rawState);
            }
        }

        foreach ($this->getChildSchemas(withHidden: true) as $childSchema) {
            $childSchema->hydrateState($hydratedDefaultState, $shouldCallHydrationHooks);
        }

        $rawState = $this->getRawState();
        $originalRawState = $rawState;

        foreach ($this->getStateCasts() as $stateCast) {
            $rawState = $stateCast->set($rawState);
        }

        if ($rawState !== $originalRawState) {
            $this->rawState($rawState);
        }

        if ($shouldCallHydrationHooks) {
            $this->callAfterStateHydrated();
        }
    }

    /**
     * @param  array<string>  $statePaths
     */
    public function hydrateStatePartially(array $statePaths, bool $shouldCallHydrationHooks = true): void
    {
        if ($this->hasStatePath()) {
            $statePathToCheck = $this->getStatePath();

            $isStatePathMatching = in_array($statePathToCheck, $statePaths);

            // Even if the current component's state path is not present in the array of state paths to hydrate,
            // a parent state path may be present. In this case, we still need to hydrate the field as it is
            // nested inside the parent state that was hydrated.
            while ((! $isStatePathMatching) && str($statePathToCheck)->contains('.')) {
                $statePathToCheck = (string) str($statePathToCheck)->beforeLast('.');

                $isStatePathMatching = in_array($statePathToCheck, $statePaths);
            }
        }

        if (! ($isStatePathMatching ?? false)) {
            foreach ($this->getChildSchemas(withHidden: true) as $childSchema) {
                $childSchema->hydrateStatePartially($statePaths, $shouldCallHydrationHooks);
            }

            return;
        }

        $this->loadStateFromRelationships();

        $rawState = $this->getRawState();

        // Hydrate all arrayable state objects as arrays by converting
        // them to collections, then using `toArray()`.
        if (is_array($rawState) || $rawState instanceof Arrayable) {
            $rawState = collect($rawState)->toArray();

            $this->rawState($rawState);
        }

        foreach ($this->getChildSchemas(withHidden: true) as $childSchema) {
            $childSchema->hydrateStatePartially($statePaths, $shouldCallHydrationHooks);
        }

        $rawState = $this->getRawState();
        $originalRawState = $rawState;

        foreach ($this->getStateCasts() as $stateCast) {
            $rawState = $stateCast->set($rawState);
        }

        if ($rawState !== $originalRawState) {
            $this->rawState($rawState);
        }

        if ($shouldCallHydrationHooks) {
            $this->callAfterStateHydrated();
        }
    }

    /**
     * @param  array<string, mixed> | null  $hydratedDefaultState
     */
    public function hydrateDefaultState(?array &$hydratedDefaultState): void
    {
        if ($hydratedDefaultState === null) {
            return;
        }

        $statePath = $this->getStatePath();

        if (Arr::has($hydratedDefaultState, $statePath)) {
            return;
        }

        if (! $this->hasDefaultState()) {
            $this->hasStatePath() && $this->state(null);

            return;
        }

        $defaultState = $this->getDefaultState();

        $this->state($defaultState);

        Arr::set($hydratedDefaultState, $statePath, $defaultState); /** @phpstan-ignore parameterByRef.type */
    }

    public function fillStateWithNull(): void
    {
        if (
            (! Arr::has((array) $this->getLivewire(), $this->getStatePath())) &&
            (! $this instanceof Entry)
        ) {
            $this->state(null);
        }

        foreach ($this->getChildSchemas(withHidden: true) as $childSchema) {
            $childSchema->fillStateWithNull();
        }
    }

    public function mutateDehydratedState(mixed $state): mixed
    {

        if (! $this->mutateDehydratedStateUsing) {
            return $state;
        }

        return $this->evaluate(
            $this->mutateDehydratedStateUsing,
            ['state' => $state],
        );
    }

    public function mutateStateForValidation(mixed $state): mixed
    {
        if (! $this->mutateStateForValidationUsing) {
            return $state;
        }

        return $this->evaluate(
            $this->mutateStateForValidationUsing,
            ['state' => $state],
        );
    }

    public function mutatesDehydratedState(): bool
    {
        return $this->mutateDehydratedStateUsing instanceof Closure;
    }

    public function mutatesStateForValidation(): bool
    {
        return $this->mutateStateForValidationUsing instanceof Closure;
    }

    public function mutateDehydratedStateUsing(?Closure $callback): static
    {
        $this->mutateDehydratedStateUsing = $callback;

        return $this;
    }

    public function mutateStateForValidationUsing(?Closure $callback): static
    {
        $this->mutateStateForValidationUsing = $callback;

        return $this;
    }

    public function state(mixed $state): static
    {
        foreach (array_reverse($this->getStateCasts()) as $stateCast) {
            $state = $stateCast->set($state);
        }

        $this->rawState($state);

        return $this;
    }

    public function rawState(mixed $state): static
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

    public function getOldState(): mixed
    {
        if (! Livewire::isLivewireRequest()) {
            return null;
        }

        $state = $this->getOldRawState();

        if (blank($state)) {
            return null;
        }

        foreach ($this->getStateCasts() as $stateCast) {
            $state = $stateCast->get($state);
        }

        return $state;
    }

    public function getOldRawState(): mixed
    {
        if (! Livewire::isLivewireRequest()) {
            return null;
        }

        $state = $this->getLivewire()->getOldSchemaState($this->getStatePath());

        if (blank($state)) {
            return null;
        }

        return $state;
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

        if (filled($containerStatePath = $this->getContainer()->getStatePath())) {
            $pathComponents[] = $containerStatePath;
        }

        if ($this->hasStatePath()) {
            $pathComponents[] = $this->statePath;
        }

        return $this->cachedAbsoluteStatePath = implode('.', $pathComponents);
    }

    /**
     * @return array<string>
     */
    public function getAfterStateUpdatedJs(): array
    {
        return array_reduce(
            $this->afterStateUpdatedJs,
            function (array $carry, string | Closure $js): array {
                $js = $this->evaluate($js);

                if (blank($js)) {
                    return $carry;
                }

                $carry[] = $js;

                return $carry;
            },
            initial: [],
        );
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
        if (! $this->evaluate($this->isDehydrated)) {
            return false;
        }

        return ! $this->isHiddenAndNotDehydratedWhenHidden();
    }

    public function isDehydratedWhenHidden(): bool
    {
        return (bool) $this->evaluate($this->isDehydratedWhenHidden);
    }

    public function isValidatedWhenNotDehydrated(): bool
    {
        return (bool) $this->evaluate($this->isValidatedWhenNotDehydrated);
    }

    public function isNeitherDehydratedNorValidated(): bool
    {
        if ($this->isHiddenAndNotDehydratedWhenHidden()) {
            return true;
        }

        if ($this->isDehydrated()) {
            return false;
        }

        return ! $this->isValidatedWhenNotDehydrated();
    }

    public function isHiddenAndNotDehydratedWhenHidden(): bool
    {
        if (! $this->isHidden()) {
            return false;
        }

        return ! $this->isDehydratedWhenHidden();
    }

    public function makeGetUtility(): Get
    {
        return app(Get::class, ['component' => $this]);
    }

    public function makeSetUtility(): Set
    {
        return app(Set::class, ['component' => $this]);
    }

    /**
     * @deprecated Use `makeGetUtility()` instead.
     */
    public function getGetCallback(): Get
    {
        return $this->makeGetUtility();
    }

    /**
     * @deprecated Use `makeSetUtility()` instead.
     */
    public function getSetCallback(): Set
    {
        return $this->makeSetUtility();
    }

    public function resolveRelativeStatePath(string | Component $path = '', bool $isAbsolute = false): string
    {
        if ($path instanceof Component) {
            return $path->getStatePath();
        }

        if (str($path)->startsWith('/')) {
            $isAbsolute = true;
            $path = (string) str($path)->after('/');
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

        return filled(ltrim($path, './')) ? "{$containerPath}.{$path}" : $containerPath;
    }

    public function resolveRelativeKey(string | Component $key = '', bool $isAbsolute = false): string
    {
        if ($key instanceof Component) {
            return $key->getKey();
        }

        if (str($key)->startsWith('/')) {
            $isAbsolute = true;
            $key = (string) str($key)->after('/');
        }

        if ($isAbsolute) {
            return $key;
        }

        $containerKey = $this->getContainer()->getKey();

        while (str($key)->startsWith('../')) {
            $containerKey = Str::contains($containerKey, '.') ?
                (string) str($containerKey)->beforeLast('.') :
                null;

            $key = (string) str($key)->after('../');
        }

        if (blank($containerKey)) {
            return $key;
        }

        return filled(ltrim($key, './')) ? "{$containerKey}.{$key}" : $containerKey;
    }

    protected function flushCachedAbsoluteStatePath(): void
    {
        /** @phpstan-ignore unset.possiblyHookedProperty */
        unset($this->cachedAbsoluteStatePath);
    }

    public function getConstantStateUsing(mixed $callback): static
    {
        $this->getConstantStateUsing = $callback;
        $this->hasConstantState = true;

        return $this;
    }

    public function constantState(mixed $state): static
    {
        $this->getConstantStateUsing($state);

        return $this;
    }

    public function distinctList(bool | Closure $condition = true): static
    {
        $this->isDistinctList = $condition;

        return $this;
    }

    public function isDistinctList(): bool
    {
        return (bool) $this->evaluate($this->isDistinctList);
    }

    public function getState(): mixed
    {
        $state = $this->getRawState();

        foreach ($this->getStateCasts() as $stateCast) {
            $state = $stateCast->get($state);
        }

        return $state;
    }

    public function getRawState(): mixed
    {
        $statePath = $this->getStatePath();

        if (blank($statePath)) {
            return [];
        }

        $state = data_get($this->getLivewire(), $statePath);

        if ((! is_array($state)) && blank($state)) {
            $state = null;
        }

        return $state;
    }

    /**
     * @internal Do not use this method outside the internals of Filament. It is subject to breaking changes in minor and patch releases.
     */
    public function getConstantState(): mixed
    {
        if ($this->hasConstantState) {
            $state = $this->evaluate($this->getConstantStateUsing);
        } else {
            $containerState = $this->getContainer()->getConstantState();

            $state = $containerState instanceof Model ?
                $this->getConstantStateFromRecord($containerState) :
                data_get($containerState, $this->getConstantStatePath());
        }

        if (is_string($state) && ($separator = $this->getSeparator())) {
            $state = explode($separator, $state);
            $state = (count($state) === 1 && blank($state[0])) ?
                [] :
                $state;
        }

        if (blank($state)) {
            $state = $this->getDefaultState();
        }

        return $state;
    }

    /**
     * @internal Do not use this method outside the internals of Filament. It is subject to breaking changes in minor and patch releases.
     */
    public function getConstantStatePath(): ?string
    {
        $statePath = $this->getStatePath();

        if (blank($statePath)) {
            return null;
        }

        $containerConstantStatePath = $this->getContainer()->getConstantStatePath();

        if (blank($containerConstantStatePath)) {
            return $statePath;
        }

        if (! str($statePath)->startsWith("{$containerConstantStatePath}.")) {
            throw new Exception("The current component\'s state path [$statePath] does not start with the container\'s constant state path [$containerConstantStatePath].");
        }

        return (string) str($statePath)->after("{$containerConstantStatePath}.");
    }

    /**
     * @internal Do not use this method outside the internals of Filament. It is subject to breaking changes in minor and patch releases.
     */
    public function getRecordConstantStatePath(): ?string
    {
        if ($this->getRecord(withContainerRecord: false)) {
            return $this->getStatePath();
        }

        return $this->getContainer()->getConstantStatePath();
    }

    public function separator(string | Closure | null $separator = ','): static
    {
        $this->separator = $separator;

        return $this;
    }

    public function getSeparator(): ?string
    {
        return $this->evaluate($this->separator);
    }

    /**
     * @internal Do not use this method outside the internals of Filament. It is subject to breaking changes in minor and patch releases.
     */
    public function getConstantStateFromRecord(Model $record): mixed
    {
        $name = $this->getConstantStatePath();

        if (
            ($record instanceof HasRichContent) &&
            $record->hasRichContentAttribute($name)
        ) {
            $state = $record->getRichContentAttribute($name);
        } else {
            $state = data_get($record, $name);
        }

        if ($state !== null) {
            return $state;
        }

        if (! $this->hasStateRelationship($record)) {
            return null;
        }

        $relationship = $this->getStateRelationship($record);

        if (! $relationship) {
            return null;
        }

        $relationshipAttribute = $this->getStateRelationshipAttribute();

        $state = collect($this->getStateRelationshipResults($record))
            ->filter(fn (Model $record): bool => array_key_exists($relationshipAttribute, $record->attributesToArray()))
            ->pluck($relationshipAttribute)
            ->filter(fn ($state): bool => filled($state))
            ->when($this->isDistinctList(), fn (Collection $state) => $state->unique())
            ->values();

        if (! $state->count()) {
            return null;
        }

        return $state->all();
    }

    public function getStatePathForRelationship(): ?string
    {
        return $this->getConstantStatePath();
    }

    public function shouldUpdateValidatedStateAfterBeforeStateDehydratedRuns(): bool
    {
        return $this->shouldUpdateValidatedStateAfterBeforeStateDehydratedRuns;
    }
}
