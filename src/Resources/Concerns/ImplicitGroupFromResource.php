<?php

namespace Filament\Resources\Concerns;

use Filament\View\NavigationGroup;

trait ImplicitGroupFromResource
{
    public static function registerNavigationGroup(): void
    {
        $groupSettings = [
            'name'  => static::getLabel(),
            'icon'  => static::getIcon(),
            'sort'  => static::getNavigationSort(),
            'resource'   => new static(),
        ];

        $otherNavItems = collect(static::navigationItems())->filter(fn($item) => static::getLabel() !== $item->label)->toArray();
        $menus = static::getGroupMenusList();
        if (1 < count($menus) || 'default' !== $menus[0]) {
            $groupSettings['menus'] = $menus;
            NavigationGroup::registerGroupWithMenus($groupSettings);
            NavigationGroup::group(static::getLabel())->push(...$otherNavItems);
            return;
        }

        NavigationGroup::registerGroup($groupSettings)->push(...$otherNavItems);
    }

    /**
     * This method helps define what menus a group should be included in.
     * @return array<string>
     */
    public static function getGroupMenusList(): array
    {
        return ['default'];
    }
}
