<?php

namespace Filament\Pages\Concerns;

use Filament\View\NavigationGroup;

trait ImplicitGroupFromPage
{
    public static function registerNavigationGroup(): void
    {
        $groupSettings = [
            'name'  => static::getNavigationLabel(),
            'icon'  => static::getIcon(),
            'sort'  => static::getNavigationSort(),
            'resource'   => new static(),
        ];


        $otherNavItems = collect(static::navigationItems())->filter(fn($item) => static::getNavigationLabel() !== $item->label)->toArray();
        $groupSettings['menus'] = static::getGroupMenusList();
        NavigationGroup::registerGroupWithMenus($groupSettings);
        NavigationGroup::group(static::getNavigationLabel())->push(...$otherNavItems);
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
