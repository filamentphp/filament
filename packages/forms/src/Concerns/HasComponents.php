<?php

namespace Filament\Forms\Concerns;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;

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

    public function getComponent(string | Closure $callback, bool $withHidden = false): ?Component
    {
        $callback = $callback instanceof Closure ?
            $callback :
            static function (Component $component) use ($callback): bool {
                $key = $component->getKey();

                if ($key === null) {
                    return false;
                }

                return $key === $callback;
            };

        return collect($this->getFlatComponents($withHidden))->first($callback);
    }

    /**
     * @return array<Component>
     */
    public function getFlatComponents(bool $withHidden = false): array
    {
        return collect($this->getComponents($withHidden))
            ->map(static fn (Component $component): array => [
                $component,
                array_map(
                    fn (ComponentContainer $container): array => $container->getFlatComponents($withHidden),
                    $component->getChildComponentContainers($withHidden),
                ),
            ])
            ->flatten()
            ->all();
    }

    /**
     * @return array<Field>
     */
    public function getFlatFields(bool $withHidden = false, bool $withAbsolutePathKeys = false): array
    {
        $statePath = $this->getStatePath();

        return collect($this->getFlatComponents($withHidden))
            ->whereInstanceOf(Field::class)
            ->mapWithKeys(static function (Field $field) use ($statePath, $withAbsolutePathKeys): array {
                $fieldStatePath = $field->getStatePath();

                if ((! $withAbsolutePathKeys) && filled($statePath)) {
                    $fieldStatePath = (string) str($fieldStatePath)->after("{$statePath}.");
                }

                return [$fieldStatePath => $field];
            })
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
