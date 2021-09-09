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

    public function getComponent(string | Closure $callback): ?Component
    {
        $callback = $callback instanceof Closure
            ? $callback
            : fn (Component $component) => $component instanceof Field && $component->getName() === $callback;

        return collect($this->components)->first($callback);
    }

    public function getComponents(): array
    {
        return array_filter($this->components, fn (Component $component) => ! $component->isHidden());
    }
}
