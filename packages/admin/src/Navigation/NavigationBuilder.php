<?php

namespace Filament\Navigation;

use Illuminate\Support\Traits\Conditionable;

class NavigationBuilder
{
    use Conditionable;

    /** @var array<string, \Filament\Navigation\NavigationItem[]> */
    protected array $groups = [];

    /** @var \Filament\Navigation\NavigationItem[] */
    protected array $items = [];

    public function group(string $name, array $items = [], bool $collapsible = true): static
    {
        $this->groups[$name] = [
            'items' => collect($items)->map(
                fn (NavigationItem $item, int $index) => $item->group($name)->sort($index),
            )->toArray(),
            'collapsible' => $collapsible,
        ];

        return $this;
    }

    public function item(NavigationItem $item): static
    {
        $this->items[] = $item;

        return $this;
    }

    /** @param  \Filament\Navigation\NavigationItem[]  $items */
    public function items(array $items): static
    {
        $this->items = array_merge($this->items, $items);

        return $this;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
