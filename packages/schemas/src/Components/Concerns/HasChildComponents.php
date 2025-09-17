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

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Closure  $components
     */
    public function schema(array | Closure $components): static
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
     * @return array<Component | Action | ActionGroup | string | Htmlable>
     */
    public function getDefaultChildComponents(): array
    {
        return $this->evaluate($this->childComponents['default'] ?? []) ?? [];
    }

    /**
     * @param  array-key  $key
     */
    public function getChildSchema($key = null): ?Schema
    {
        if (filled($key) && array_key_exists($key, $containers = $this->getDefaultChildSchemas())) {
            return $containers[$key];
        }

        $key ??= 'default';

        $components = ($key === 'default')
            ? $this->getDefaultChildComponents()
            : $this->evaluate($this->childComponents[$key] ?? []) ?? [];

        if (blank($components)) {
            return ($key === 'default')
                ? $this->configureChildSchema(
                    $this->makeChildSchema($key),
                    $key,
                )
                : null;
        }

        if ($components instanceof Schema) {
            return $this->configureChildSchema(
                $components
                    ->livewire($this->getLivewire())
                    ->parentComponent($this),
                $key,
            );
        }

        return $this->configureChildSchema(
            $this->makeChildSchema($key)
                ->components($components),
            $key,
        );
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
            ->parentComponent($this);
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
            ...(array_key_exists('default', $this->childComponents) ? $this->getDefaultChildSchemas() : []),
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

    protected function cloneChildComponents(): static
    {
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
                ($childComponents instanceof ActionGroup)
            ) {
                $this->childComponents[$key] = $childComponents->getClone();
            }
        }

        return $this;
    }
}
