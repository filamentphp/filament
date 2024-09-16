<?php

namespace Filament\Navigation;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;

class NavigationBuilder
{
    use Conditionable;

    /** @var array<NavigationGroup> */
    protected array $groups = [];

    /** @var array<NavigationItem> */
    protected array $items = [];

    /**
     * @param  array<NavigationItem>  $items
     */
    public function group(NavigationGroup | string $group, array $items = [], ?bool $collapsible = null): static
    {
        if ($group instanceof NavigationGroup) {
            $this->groups[] = $group;

            return $this;
        }

        $this->groups[] = NavigationGroup::make($group)
            ->items($items)
            ->collapsible($collapsible);

        return $this;
    }

    public function item(NavigationItem $item): static
    {
        $this->items[] = $item;

        return $this;
    }

    /** @param  array<NavigationGroup>  $groups */
    public function groups(array $groups): static
    {
        $this->groups = [
            ...$this->groups,
            ...$groups,
        ];

        return $this;
    }

    /** @param  array<NavigationItem>  $items */
    public function items(array $items): static
    {
        $this->items = [
            ...$this->items,
            ...$items,
        ];

        return $this;
    }

    /**
     * @return array<NavigationGroup>
     */
    public function getNavigation(): array
    {
        $items = array_filter(
            $this->items,
            fn (NavigationItem $item): bool => $item->isVisible(),
        );

        return collect($this->groups)
            ->filter(function (NavigationGroup $group): bool {
                $visibleGroupItems = array_filter(
                    $group->getItems(),
                    fn (NavigationItem $item): bool => $item->isVisible(),
                );

                if (empty($visibleGroupItems)) {
                    return false;
                }

                $group->items($visibleGroupItems);

                return true;
            })
            ->when(
                count($items),
                fn (Collection $groups): Collection => $groups->prepend(
                    NavigationGroup::make()->items($items),
                ),
            )
            ->all();
    }
}
