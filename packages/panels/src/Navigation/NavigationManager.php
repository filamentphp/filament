<?php

namespace Filament\Navigation;

use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class NavigationManager
{
    protected Panel $panel;

    protected bool $isNavigationMounted = false;

    /**
     * @var array<string | int, NavigationGroup | string>
     */
    protected array $navigationGroups = [];

    /**
     * @var array<NavigationItem>
     */
    protected array $navigationItems = [];

    public function __construct()
    {
        $this->panel = Filament::getCurrentPanel();

        $this->navigationGroups = array_map(
            fn (NavigationGroup | string $group): NavigationGroup | string => $group instanceof NavigationGroup ? (clone $group) : $group,
            $this->panel->getNavigationGroups(),
        );
        $this->navigationItems = array_map(
            fn (NavigationItem $item): NavigationItem => clone $item,
            $this->panel->getNavigationItems(),
        );
    }

    /**
     * @return array<NavigationGroup>
     */
    public function get(): array
    {
        if ($this->panel->hasNavigationBuilder()) {
            return $this->panel->buildNavigation();
        }

        if (!$this->isNavigationMounted) {
            $this->mountNavigation();
        }

        $groups = collect($this->getNavigationGroups())
            ->sortBy(fn(NavigationGroup $group) => $group->getOrder() ?? PHP_INT_MAX); // Sort groups by their order

        return collect($this->getNavigationItems())
            ->filter(fn (NavigationItem $item): bool => $item->isVisible())
            ->sortBy(fn(NavigationItem $item) => $item->getSort())
            ->groupBy(fn(NavigationItem $item) => $item->getGroup() ?? '')
            ->map(function (Collection $items, string $groupIndex) use ($groups): NavigationGroup {

                $parentItems = $items->groupBy(fn(NavigationItem $item) => $item->getParentItem() ?? '');

                $items = $parentItems->get('')
                    ->keyBy(fn(NavigationItem $item) => $item->getLabel())
                    ->sortBy(fn(NavigationItem $item) => $item->getSort() ?? PHP_INT_MAX); // Sort items by their order

                $parentItems->except([''])->each(function (Collection $parentItemItems, string $parentItemLabel) use ($items) {
                    if (!$items->has($parentItemLabel)) {
                        return;
                    }

                    $items->get($parentItemLabel)->childItems($parentItemItems);
                });

                if (blank($groupIndex)) {
                    return NavigationGroup::make()->items($items);
                }

                $registeredGroup = $groups
                    ->first(function (NavigationGroup|string $registeredGroup) use ($groupIndex) {
                        if ($registeredGroup instanceof NavigationGroup && $registeredGroup->getLabel() === $groupIndex) {
                            return true;
                        }
                        return false;
                    });

                if ($registeredGroup instanceof NavigationGroup) {
                    return $registeredGroup->items($items);
                }

                return NavigationGroup::make($groupIndex)->items($items);
            })
            ->sortBy(function (NavigationGroup $group) {

                if (blank($group->getLabel())) {
                    return -1;
                }
                return $group->getOrder() ?? PHP_INT_MAX;


            })
            ->values()
            ->all();
    }

    public function mountNavigation(): void
    {
        foreach ($this->panel->getPages() as $page) {
            $page::registerNavigationItems();
        }

        foreach ($this->panel->getResources() as $resource) {
            $resource::registerNavigationItems();
        }

        $this->isNavigationMounted = true;
    }

    /**
     * @param  array<string | int, NavigationGroup | string>  $groups
     */
    public function navigationGroups(array $groups): static
    {
        $this->navigationGroups = [
            ...$this->navigationGroups,
            ...$groups,
        ];

        return $this;
    }

    /**
     * @param  array<NavigationItem>  $items
     */
    public function navigationItems(array $items): static
    {
        $this->navigationItems = [
            ...$this->navigationItems,
            ...$items,
        ];

        return $this;
    }

    /**
     * @return array<string | int, NavigationGroup | string>
     */
    public function getNavigationGroups(): array
    {
        return $this->navigationGroups;
    }

    /**
     * @return array<NavigationItem>
     */
    public function getNavigationItems(): array
    {
        return $this->navigationItems;
    }
}
