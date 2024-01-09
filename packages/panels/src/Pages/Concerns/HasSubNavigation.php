<?php

namespace Filament\Pages\Concerns;

use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page as ResourcePage;

trait HasSubNavigation
{
    /**
     * @var array<NavigationGroup>
     */
    protected array $cachedSubNavigation;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    /**
     * @return array<NavigationItem | NavigationGroup>
     */
    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    public function getSubNavigationPosition(): SubNavigationPosition
    {
        return static::$subNavigationPosition;
    }

    /**
     * @return array<NavigationGroup>
     */
    public function getCachedSubNavigation(): array
    {
        if (isset($this->cachedSubNavigation)) {
            return $this->cachedSubNavigation;
        }

        [$navigationItems, $navigationGroups] = array_reduce($this->getSubNavigation(), function (array $groups, NavigationItem | NavigationGroup $item) {
            $groups[$item instanceof NavigationItem ? 0 : 1][] = $item;

            return $groups;
        }, [[], []]);

        return $this->cachedSubNavigation ??= [
            ...($navigationItems ? [NavigationGroup::make()->items($navigationItems)] : []),
            ...$navigationGroups,
        ];
    }

    /**
     * @param  array<class-string<Page>>  $components
     * @return array<NavigationItem>
     */
    public function generateNavigationItems(array $components): array
    {
        $parameters = $this->getSubNavigationParameters();

        $items = [];

        foreach ($components as $component) {
            $isResourcePage = is_subclass_of($component, ResourcePage::class);

            $shouldRegisterNavigation = $isResourcePage ?
                $component::shouldRegisterNavigation($parameters) :
                $component::shouldRegisterNavigation();

            if (! $shouldRegisterNavigation) {
                continue;
            }

            if (! $component::canAccess()) {
                continue;
            }

            $pageItems = $isResourcePage ?
                $component::getNavigationItems($parameters) :
                $component::getNavigationItems();

            $items = [
                ...$items,
                ...$pageItems,
            ];
        }

        return $items;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSubNavigationParameters(): array
    {
        return [];
    }
}
