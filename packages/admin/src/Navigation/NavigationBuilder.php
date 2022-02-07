<?php

namespace Filament\Navigation;

class NavigationBuilder
{
    /** @var array<string, \Filament\Navigation\NavigationItem[]> */
    protected array $groups = [];

    /** @var \Filament\Navigation\NavigationItem[] */
    protected array $items = [];

    public function group(string $name, array $items = []): static
    {
        $this->groups[$name] = collect($items)->map(
            fn (NavigationItem $item, int $index) => $item->group($name)->sort($index),
        )->toArray();

        return $this;
    }

    public function item(NavigationItem $item): static
    {
        $this->items[] = $item;

        return $this;
    }

    /** @param \Filament\Navigation\NavigationItem[] $items */
    public function items(array $items): static
    {
        foreach ($items as $item) {
            $this->item($item);
        }

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
