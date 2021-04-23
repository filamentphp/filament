<?php

namespace Filament\Resources\Concerns;

use Filament\View\NavigationGroup;
use Illuminate\Support\Str;

trait ImplicitGroupFromResource
{
    public static function registerNavigationGroup(): void
    {
        $groupSettings = [
            'name'  => self::getLabel(),
            'icon'  => self::getIcon(),
            'sort'  => self::getNavigationSort(),
            'resource'   => new static(),
        ];

        $otherNavItems = collect(self::navigationItems())->filter(fn($item) => self::getLabel() !== $item->label)->toArray();
        NavigationGroup::registerGroup($groupSettings)->push(...$otherNavItems);
    }
}
