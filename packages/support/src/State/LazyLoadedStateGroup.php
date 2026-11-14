<?php

namespace Filament\Support\State;

use Closure;
use Filament\Support\Components\Component;

class LazyLoadedStateGroup extends Component
{
    protected Closure $getStateUsing;

    final public function __construct(protected string | Closure $name) {}

    public static function make(string | Closure $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public function state(Closure $callback): static
    {
        $this->getStateUsing = $callback;

        return $this;
    }

    public function getName(Component $component): string
    {
        return (string) $component->evaluate($this->name);
    }

    /**
     * @param  array<string, Component>  $components
     * @param  array<string, mixed>  $records
     * @return array<string, mixed>
     */
    public function getState(Component $component, array $components, array $records): array
    {
        return $component->evaluate($this->getStateUsing, [
            'components' => $components,
            'records' => $records,
        ]);
    }
}
