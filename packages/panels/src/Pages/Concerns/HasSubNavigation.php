<?php

namespace Filament\Pages\Concerns;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Pages\Page;
use Filament\Resources\Pages\Page as ResourcePage;
use Illuminate\Support\Collection;
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
                $itemGroupKey = $itemGroup;

                if ($itemGroup instanceof UnitEnum) {
                    $itemGroupKey = $itemGroup->name;
                }

                if (array_key_exists($itemGroupKey, $navigationGroups)) {
                    $navigationGroups[$itemGroupKey]->items([
                        ...$navigationGroups[$itemGroupKey]->getItems(),
                        $item,
                    ]);

                    return false;
                }

                if (filled($itemGroup)) {
                    $navigationGroups[$itemGroupKey] = ($itemGroup instanceof UnitEnum)
                        ? NavigationGroup::fromEnum($itemGroup)->items([$item])
                        : NavigationGroup::make()->label($itemGroup)->items([$item]);

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

        $navigationItems = $this->processParentNavigationItems(collect($navigationItems))->all();

        foreach ($navigationGroups as $navigationGroup) {
            $navigationGroup->items(
                $this->processParentNavigationItems(collect($navigationGroup->getItems()))->all(),
            );
        }

        return $this->cachedSubNavigation = [
            ...($navigationItems ? [NavigationGroup::make()->items($navigationItems)] : []),
            ...$navigationGroups,
        ];
    }

    /**
     * @param  Collection<int, NavigationItem>  $items
     * @return Collection<int, NavigationItem>
     */
    protected function processParentNavigationItems(Collection $items): Collection
    {
        $parentItems = $items->groupBy(fn (NavigationItem $item): string => $item->getParentItem() ?? '');

        $items = $parentItems->get('', collect())
            ->keyBy(fn (NavigationItem $item): string => $item->getLabel());

        $parentItems->except([''])->each(function (Collection $childItems, string $parentItemLabel) use ($items): void {
            if (! $items->has($parentItemLabel)) {
                return;
            }

            $items->get($parentItemLabel)->childItems($childItems);
        });

        return $items->values();
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
