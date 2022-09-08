<?php

namespace Filament\Forms\Concerns;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Illuminate\Support\Str;

trait HasComponents
{
    protected array | Closure $components = [];

    public function components(array | Closure $components): static
    {
        $this->components = $components;

        return $this;
    }

    public function schema(array | Closure $components): static
    {
        $this->components($components);

        return $this;
    }

    public function getComponent(string | Closure $callback, bool $withHidden = false): ?Component
    {
        $callback = $callback instanceof Closure
             ? $callback
             : fn (Component $component): bool => $component instanceof Field && $component->getStatePath() === $callback;

        return collect($this->getFlatComponents($withHidden))->first($callback);
    }

    public function getFlatComponents(bool $withHidden = false): array
    {
        return collect($this->getComponents($withHidden))
            ->map(static function (Component $component) use ($withHidden) {
                if ($component->hasChildComponentContainer($withHidden)) {
                    return [
                        $component,
                        array_map(
                            fn (ComponentContainer $container): array => $container->getFlatComponents($withHidden),
                            $component->getChildComponentContainers($withHidden),
                        ),
                    ];
                }

                return $component;
            })
            ->flatten()
            ->all();
    }

    public function getFlatFields(bool $withHidden = false, bool $withAbsolutePathKeys = false): array
    {
        $statePath = $this->getStatePath();

        return collect($this->getFlatComponents($withHidden))
            ->whereInstanceOf(Field::class)
            ->mapWithKeys(static function (Field $field) use ($statePath, $withAbsolutePathKeys): array {
                $fieldStatePath = $field->getStatePath();

                if ((! $withAbsolutePathKeys) && filled($statePath)) {
                    $fieldStatePath = (string) Str::of($fieldStatePath)->after("{$statePath}.");
                }

                return [$fieldStatePath => $field];
            })
            ->all();
    }

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
            fn (Component $component) => ! $component->isHidden(),
        );
    }
}
