<?php

namespace Filament\Schema\Concerns;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Schema\Components\Component;

trait HasComponents
{
    /**
     * @var array<Component> | Closure
     */
    protected array | Closure $components = [];

    /**
     * @var array<string, Component>
     */
    protected array $cachedVisibleComponents;

    /**
     * @var array<array<array<array<string, Component>>>>
     */
    protected array $cachedFlatComponents = [];

    /**
     * @param  array<Component> | Closure  $components
     */
    public function components(array | Closure $components): static
    {
        $this->components = $components;

        return $this;
    }

    /**
     * @param  array<Component> | Closure  $components
     */
    public function schema(array | Closure $components): static
    {
        $this->components($components);

        return $this;
    }

    public function getComponent(string | Closure $findComponentUsing, bool $withHidden = false, bool $isAbsoluteKey = false): ?Component
    {
        if (! is_string($findComponentUsing)) {
            return collect($this->getFlatComponents($withHidden))->first($findComponentUsing);
        }

        if ($withHidden) {
            return $this->getFlatComponents($withHidden)[$findComponentUsing] ?? null;
        }

        if ((! $isAbsoluteKey) && filled($key = $this->getKey())) {
            $findComponentUsing = "{$key}.{$findComponentUsing}";
        }

        return $this->getCachedVisibleComponents()[$findComponentUsing] ?? null;
    }

    /**
     * @return array<string, Component>
     */
    public function cacheVisibleComponents(): array
    {
        $this->cachedVisibleComponents = [];

        foreach ($this->getComponents() as $component) {
            if (filled($componentKey = $component->getKey())) {
                $this->cachedVisibleComponents[$componentKey] = $component;
            }

            foreach ($component->getChildComponentContainers() as $childComponentContainer) {
                $this->cachedVisibleComponents = [
                    ...$this->cachedVisibleComponents,
                    ...$childComponentContainer->getCachedVisibleComponents(),
                ];
            }
        }

        return $this->cachedVisibleComponents;
    }

    /**
     * @return array<string, Component>
     */
    public function getCachedVisibleComponents(): array
    {
        return $this->cachedVisibleComponents ??= $this->cacheVisibleComponents();
    }

    /**
     * @return array<Component>
     */
    public function getFlatComponents(bool $withHidden = false, bool $withAbsoluteKeys = false, ?string $containerKey = null): array
    {
        $containerKey ??= $this->getKey();

        return $this->cachedFlatComponents[$withHidden][$withAbsoluteKeys][$containerKey] ??= array_reduce(
            $this->getComponents($withHidden),
            function (array $carry, Component $component) use ($containerKey, $withHidden, $withAbsoluteKeys): array {
                $componentKey = $component->getKey();

                if (blank($componentKey)) {
                    $carry[] = $component;
                } elseif ((! $withAbsoluteKeys) && filled($containerKey)) {
                    $carry[(string) str($componentKey)->after("{$containerKey}.")] = $component;
                } else {
                    $carry[$componentKey] = $component;
                }

                foreach ($component->getChildComponentContainers($withHidden) as $childComponentContainer) {
                    $carry = [
                        ...$carry,
                        ...$childComponentContainer->getFlatComponents($withHidden, $withAbsoluteKeys, $containerKey),
                    ];
                }

                return $carry;
            },
            initial: [],
        );
    }

    /**
     * @return array<Field>
     */
    public function getFlatFields(bool $withHidden = false, bool $withAbsoluteKeys = false): array
    {
        return collect($this->getFlatComponents($withHidden, $withAbsoluteKeys))
            ->whereInstanceOf(Field::class)
            ->all();
    }

    /**
     * @return array<Component>
     */
    public function getComponents(bool $withHidden = false): array
    {
        $components = array_map(function (Component $component): Component {
            $component->container($this);

            return $component;
        }, $this->evaluate($this->components));

        if ($withHidden) {
            return $components;
        }

        return collect($components)
            ->filter(fn (Component $component) => $component->isVisible())
            ->values()
            ->all();
    }
}
