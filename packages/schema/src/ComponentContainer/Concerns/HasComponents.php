<?php

namespace Filament\Schema\ComponentContainer\Concerns;

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

    public function getComponent(string | Closure $findComponentUsing, bool $withHidden = false): ?Component
    {
        $components = $this->getFlatComponents($withHidden);

        if (is_string($findComponentUsing)) {
            return $components[$findComponentUsing] ?? null;
        }

        return collect($components)->first($findComponentUsing);
    }

    /**
     * @return array<Component>
     */
    public function getFlatComponents(bool $withHidden = false, bool $withAbsoluteKeys = false, ?string $containerKey = null): array
    {
        $containerKey ??= $this->getKey();

        return array_reduce(
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

        return array_filter(
            $components,
            fn (Component $component) => $component->isVisible(),
        );
    }
}
