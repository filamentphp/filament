<?php

namespace Filament\Navigation;

use Illuminate\Support\Traits\Conditionable;

class NavigationBuilder
{
    use Conditionable;

    /** @var array<\Filament\Navigation\NavigationGroup> */
    protected array $groups = [];

    /** @var \Filament\Navigation\NavigationItem[] */
    protected array $items = [];

    public function group(string | NavigationGroup $nameOrGroup, array $items = [], bool $collapsible = true): static
    {
        if ($nameOrGroup instanceof NavigationGroup) {
            $group = $nameOrGroup;
        } else {
            $group = NavigationGroup::make()
                ->label($nameOrGroup)
                ->items($items)
                ->collapsible($collapsible)
                ->sort(count($this->groups));
        }
        $this->groups[] = $group;

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
