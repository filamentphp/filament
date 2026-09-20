<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

trait HasChildComponents
{
    /**
     * @var array<string, array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null>
     */
    protected array $childComponents = [];

    /**
     * @var array<Schema> | null
     */
    protected ?array $cachedDefaultChildSchemas = null;

    /**
     * @var array<int, array-key>
     */
    protected array $cachedDefaultChildSchemaKeys = [];

    /**
     * @var array<string, Schema>
     */
    protected array $cachedChildSchemas = [];

    protected bool $isEvaluatingDefaultChildSchemas = false;

    /**
     * @var array<string, bool>
     */
    protected array $evaluatingChildSchemaKeys = [];

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Closure  $components
     */
    public function components(array | Closure $components): static
    {
        $this->childComponents($components);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function childComponents(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components, string $key = 'default'): static
    {
        $this->childComponents[$key] = $components;
        unset($this->cachedChildSchemas[$key]);

        if ($key === 'default') {
            $this->cachedDefaultChildSchemas = null;
            $this->cachedDefaultChildSchemaKeys = [];
        }

        $this->clearCachedComponentsByStatePath();

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Closure  $components
     */
    public function schema(array | Schema | Closure $components): static
    {
        $this->childComponents($components);

        return $this;
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getChildComponents(?string $key = null): array
    {
        return $this->getChildSchema($key)->getComponents();
    }

    /**
     * @return array<Component | Action | ActionGroup | string | Htmlable> | Schema
     */
    public function getDefaultChildComponents(): array | Schema
    {
        $wasEvaluatingDefaultChildSchemas = $this->isEvaluatingDefaultChildSchemas;
        $this->isEvaluatingDefaultChildSchemas = true;

        try {
            return $this->evaluate($this->childComponents['default'] ?? []) ?? [];
        } finally {
            $this->isEvaluatingDefaultChildSchemas = $wasEvaluatingDefaultChildSchemas;
        }
    }

    /**
     * @param  array-key  $key
     */
    public function getChildSchema($key = null): ?Schema
    {
        if (filled($key) && ! array_key_exists($key, $this->childComponents)) {
            return $this->getCachedDefaultChildSchemas()[$key] ?? null;
        }

        if (filled($key) && array_key_exists($key, $cachedDefaultChildSchemas = $this->getCachedDefaultChildSchemas())) {
            return $cachedDefaultChildSchemas[$key];
        }

        $key ??= 'default';

        if (isset($this->evaluatingChildSchemaKeys[$key])) {
            return null;
        }

        $isCacheable = ($key !== 'default')
            && filled($this->childComponents[$key] ?? null)
            && ! (($this->childComponents[$key] ?? null) instanceof Closure);

        if ($isCacheable && isset($this->cachedChildSchemas[$key])) {
            return $this->cachedChildSchemas[$key];
        }

        if ($key === 'default') {
            $components = $this->getDefaultChildComponents();
        } elseif (($this->childComponents[$key] ?? null) instanceof Closure) {
            $this->evaluatingChildSchemaKeys[$key] = true;

            try {
                $components = $this->evaluate($this->childComponents[$key]) ?? [];
            } finally {
                unset($this->evaluatingChildSchemaKeys[$key]);
            }
        } else {
            $components = $this->evaluate($this->childComponents[$key] ?? []) ?? [];
        }

        if (blank($components)) {
            return ($key === 'default')
                ? $this->configureChildSchema(
                    $this->makeChildSchema($key),
                    $key,
                )
                : null;
        }

        if ($components instanceof Schema) {
            $childSchema = $this->configureChildSchema(
                $components
                    ->livewire($this->getLivewire())
                    ->parentComponent($this),
                $key,
            );
        } else {
            $childSchema = $this->configureChildSchema(
                $this->makeChildSchema($key)
                    ->components($components),
                $key,
            );
        }

        if ($isCacheable) {
            $this->cachedChildSchemas[$key] = $childSchema;
        }

        return $childSchema;
    }

    /**
     * @deprecated Use `getChildSchema()` instead.
     *
     * @param  array-key  $key
     */
    public function getChildComponentContainer($key = null): ?Schema
    {
        return $this->getChildSchema($key);
    }

    protected function makeChildSchema(string $key): Schema
    {
        return Schema::make($this->getLivewire())
            ->parentComponent($this, shouldFlushCachedHierarchy: false);
    }

    protected function configureChildSchema(Schema $schema, string $key): Schema
    {
        return $schema;
    }

    /**
     * @return array<Schema>
     */
    public function getChildSchemas(bool $withHidden = false): array
    {
        if ((! $withHidden) && $this->isHidden()) {
            return [];
        }

        return [
            ...(array_key_exists('default', $this->childComponents) ? $this->getCachedDefaultChildSchemas() : []),
            ...array_reduce(
                array_keys($this->childComponents),
                function (array $carry, string $key): array {
                    if ($key === 'default') {
                        return $carry;
                    }

                    if ($container = $this->getChildSchema($key)) {
                        $carry[$key] = $container;
                    }

                    return $carry;
                },
                initial: [],
            ),
        ];
    }

    /**
     * @return array<Schema>
     *
     * @internal This method is not part of the public API and should not be used. Its parameters may change at any time without notice.
     */
    public function getCachedChildSchemas(bool $withHidden = false): array
    {
        if ((! $withHidden) && $this->isHidden()) {
            return [];
        }

        return array_filter([
            ...((($this->cachedDefaultChildSchemas !== null) && (! $this->isEvaluatingDefaultChildSchemas) && $this->areCachedDefaultChildSchemasFreshWithoutRecursion()) ? $this->cachedDefaultChildSchemas : []),
            ...$this->cachedChildSchemas,
        ], static fn (Schema $schema): bool => $schema->hasCachedComponents());
    }

    /**
     * @internal This method is not part of the public API and should not be used. Its parameters may change at any time without notice.
     */
    public function isChildSchemaCached(Schema $schema, bool $withHidden = false): bool
    {
        if (((! $withHidden) && $this->isHidden()) || (! $schema->hasCachedComponents())) {
            return false;
        }

        $defaultChildSchemaKey = $this->cachedDefaultChildSchemaKeys[spl_object_id($schema)] ?? null;

        if ($defaultChildSchemaKey !== null) {
            return (! $this->isEvaluatingDefaultChildSchemas)
                && (($this->cachedDefaultChildSchemas[$defaultChildSchemaKey] ?? null) === $schema)
                && $this->isCachedDefaultChildSchemaFreshWithoutRecursion($defaultChildSchemaKey);
        }

        return in_array($schema, $this->cachedChildSchemas, strict: true);
    }

    /**
     * @deprecated Use `getChildSchemas()` instead.
     *
     * @return array<Schema>
     */
    public function getChildComponentContainers(bool $withHidden = false): array
    {
        return $this->getChildSchemas($withHidden);
    }

    /**
     * @return array<Schema>
     */
    public function getDefaultChildSchemas(): array
    {
        return ['default' => $this->getChildSchema()];
    }

    /**
     * @return array<Schema>
     */
    protected function getCachedDefaultChildSchemas(): array
    {
        if ($this->isEvaluatingDefaultChildSchemas) {
            return [];
        }

        if ($this->cachedDefaultChildSchemas !== null) {
            if ($this->areCachedDefaultChildSchemasFreshWithoutRecursion()) {
                return $this->cachedDefaultChildSchemas;
            }

            $this->clearCachedComponentsByStatePath();
            $this->cachedDefaultChildSchemas = null;
            $this->cachedDefaultChildSchemaKeys = [];
        }

        $this->isEvaluatingDefaultChildSchemas = true;

        try {
            $this->cachedDefaultChildSchemas = $this->getDefaultChildSchemas();
            $this->cachedDefaultChildSchemaKeys = [];

            foreach ($this->cachedDefaultChildSchemas as $key => $schema) {
                $this->cachedDefaultChildSchemaKeys[spl_object_id($schema)] = $key;
            }

            return $this->cachedDefaultChildSchemas;
        } finally {
            $this->isEvaluatingDefaultChildSchemas = false;
        }
    }

    /**
     * Components whose child schemas are derived from state, such as repeaters,
     * can override this method to compare the current state against a snapshot
     * taken when the cache was built, so that the cache invalidates itself when
     * the state changes, without an explicit `clearCachedChildSchemas()` call.
     */
    protected function areCachedDefaultChildSchemasFresh(): bool
    {
        return true;
    }

    protected function isCachedDefaultChildSchemaFresh(string | int $key): bool
    {
        return $this->areCachedDefaultChildSchemasFresh();
    }

    protected function isCachedDefaultChildSchemaFreshWithoutRecursion(string | int $key): bool
    {
        $this->isEvaluatingDefaultChildSchemas = true;

        try {
            return $this->isCachedDefaultChildSchemaFresh($key);
        } finally {
            $this->isEvaluatingDefaultChildSchemas = false;
        }
    }

    protected function areCachedDefaultChildSchemasFreshWithoutRecursion(): bool
    {
        $this->isEvaluatingDefaultChildSchemas = true;

        try {
            return $this->areCachedDefaultChildSchemasFresh();
        } finally {
            $this->isEvaluatingDefaultChildSchemas = false;
        }
    }

    public function clearCachedChildSchemas(): void
    {
        $hasCachedChildComponents = false;

        foreach ([...($this->cachedDefaultChildSchemas ?? []), ...$this->cachedChildSchemas] as $cachedChildSchema) {
            if ($cachedChildSchema->hasCachedComponents() && ($cachedChildSchema->getComponents(withActions: false, withHidden: true) !== [])) {
                $hasCachedChildComponents = true;

                break;
            }
        }

        $this->cachedDefaultChildSchemas = null;
        $this->cachedDefaultChildSchemaKeys = [];
        $this->cachedChildSchemas = [];

        if ($hasCachedChildComponents) {
            $this->clearCachedComponentsByStatePath();
        }
    }

    protected function clearCachedComponentsByStatePath(): void
    {
        if (isset($this->container)) {
            $this->getContainer()->clearCachedComponentsByStatePath();
        }
    }

    /**
     * @internal This method is not part of the public API and should not be used. Its parameters may change at any time without notice.
     */
    protected function flushCachedChildSchemaHierarchies(): void
    {
        $childSchemas = [
            ...($this->cachedDefaultChildSchemas ?? []),
            ...$this->cachedChildSchemas,
        ];

        foreach ($this->childComponents as $childComponents) {
            if ($childComponents instanceof Schema) {
                $childSchemas[] = $childComponents;
            }
        }

        $flushedChildSchemas = [];

        foreach ($childSchemas as $childSchema) {
            $childSchemaId = spl_object_id($childSchema);

            if (isset($flushedChildSchemas[$childSchemaId])) {
                continue;
            }

            $childSchema->flushCachedHierarchy();
            $flushedChildSchemas[$childSchemaId] = true;
        }
    }

    /**
     * @deprecated Use `clearCachedChildSchemas()` instead.
     */
    public function clearCachedDefaultChildSchemas(): void
    {
        $this->clearCachedChildSchemas();
    }

    protected function cloneChildComponents(): static
    {
        $this->cachedDefaultChildSchemas = null;
        $this->cachedDefaultChildSchemaKeys = [];
        $this->cachedChildSchemas = [];
        $this->evaluatingChildSchemaKeys = [];

        foreach ($this->childComponents as $key => $childComponents) {
            if (is_array($childComponents)) {
                $this->childComponents[$key] = array_map(
                    fn (Component | Action | ActionGroup | string | Htmlable $component): Component | Action | ActionGroup | string | Htmlable => match (true) {
                        $component instanceof Component, $component instanceof Action, $component instanceof ActionGroup => $component->getClone(),
                        default => $component,
                    },
                    $childComponents,
                );
            } elseif (
                ($childComponents instanceof Component) ||
                ($childComponents instanceof Action) ||
                ($childComponents instanceof ActionGroup) ||
                ($childComponents instanceof Schema)
            ) {
                $this->childComponents[$key] = $childComponents->getClone();
            }
        }

        return $this;
    }
}
