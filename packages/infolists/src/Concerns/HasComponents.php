<?php

namespace Filament\Infolists\Concerns;

use Closure;
use Filament\Infolists\Components\Component;

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
