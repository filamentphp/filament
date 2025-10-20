<?php

namespace Filament\Pages\Concerns;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Pages\Page;
use Filament\Resources\Pages\Page as ResourcePage;
use UnitEnum;

trait HasSubNavigation
{
    /**
     * @var array<NavigationGroup>
     */
    protected array $cachedSubNavigation;

    protected static ?SubNavigationPosition $subNavigationPosition = null;

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

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        if (filled(static::$subNavigationPosition)) {
            return static::$subNavigationPosition;
        }

        if (filled($cluster = static::getCluster())) {
            return $cluster::getSubNavigationPosition();
        }

        return Filament::getSubNavigationPosition();
    }

    /**
     * @return array<NavigationGroup>
     */
    public function getCachedSubNavigation(): array
    {
        if (isset($this->cachedSubNavigation)) {
            return $this->cachedSubNavigation;
        }

        $navigationItems = [];

        $navigationGroups = [];

        foreach ($this->getSubNavigation() as $item) {
            if ($item instanceof NavigationGroup) {
                $navigationGroups[$item->getLabel()] = $item;

                continue;
            }

            $navigationItems[] = $item;
        }

        $navigationItems = collect($navigationItems)
            ->sortBy(fn (NavigationItem $item): int => $item->getSort())
            ->filter(function (NavigationItem $item) use (&$navigationGroups): bool {
                if (! $item->isVisible()) {
                    return false;
                }

                $itemGroup = $item->getGroup();

                if ($itemGroup instanceof UnitEnum) {
                    $itemGroup = $itemGroup->name;
                }

                if (array_key_exists($itemGroup, $navigationGroups)) {
                    $navigationGroups[$itemGroup]->items([
                        ...$navigationGroups[$itemGroup]->getItems(),
                        $item,
                    ]);

                    return false;
                }

                if (filled($itemGroup)) {
                    $navigationGroups[$itemGroup] = NavigationGroup::make()
                        ->label($itemGroup)
                        ->items([$item]);

                    return false;
                }

                return true;
            })
            ->all();

        foreach ($navigationGroups as $navigationGroup) {
            $navigationGroup->items(
                collect($navigationGroup->getItems())
                    ->filter(fn (NavigationItem $item): bool => $item->isVisible())
                    ->sortBy(fn (NavigationItem $item): int => $item->getSort())
                    ->all(),
            );
        }

        return $this->cachedSubNavigation = [
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

            $canAccess = $isResourcePage ?
                $component::canAccess($parameters) :
                $component::canAccess();

            if (! $canAccess) {
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
