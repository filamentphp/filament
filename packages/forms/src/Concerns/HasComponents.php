<?php

namespace Filament\Forms\Concerns;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;

trait HasComponents
{
    protected array $components = [];

    public function components(array $components): static
    {
        $this->components = array_map(function (Component $component): Component {
            $component->container($this);

            return $component;
        }, $components);

        return $this;
    }

    public function schema(array $components): static
    {
        $this->components($components);

        return $this;
    }

    public function getComponent(string | callable $callback): ?Component
    {
        $callback = $callback instanceof Closure
             ? $callback
             : fn (Component $component): bool => $component instanceof Field && $component->getStatePath() === $callback;

        return collect($this->getFlatComponents())->first($callback);
    }

    public function getFlatComponents(): array
    {
        return collect($this->getComponents())
            ->map(function (Component $component) {
                if ($component->hasChildComponentContainer()) {
                    return array_merge([$component], $component->getChildComponentContainer()->getFlatComponents());
                }

                return $component;
            })
            ->flatten()
            ->all();
    }

    public function getFlatFields(): array
    {
        return collect($this->getFlatComponents())
            ->whereInstanceOf(Field::class)
            ->mapWithKeys(fn (Field $field) => [
                $field->getName() => $field,
            ])
            ->all();
    }

    public function getComponents(): array
    {
        return array_filter($this->components, fn (Component $component) => ! $component->isHidden());
    }
}
