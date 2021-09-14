<?php

namespace Filament\Forms\Concerns;

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
        $fields = $this->getFlatFields();
        
        if (is_callable($callback)) {
            return collect($fields)->first($callback);
        }
        
        return $fields[$callback] ?? null;
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
