<?php

namespace Filament\Tests\Upgrade\Fixtures\MenuItemsManual;

use Filament\Navigation\MenuItem;
use Filament\Panel;

class CustomMenuItem extends MenuItem {}

class Unrelated
{
    public function userMenuItems(array $items): void {}
}

function manual(Panel $panel, string $key, string $method, array $items): void
{
    MenuItem::configureUsing(static fn (MenuItem $item) => $item->color('danger'));
    $stored = MenuItem::make()->label('Stored');
    $panel->userMenuItems([
        $stored,
        $key => MenuItem::make()->label('Dynamic key'),
        CustomMenuItem::make()->label('Subclass'),
        MenuItem::make()->{$method}('Dynamic method'),
        MenuItem::make()->label(...$items),
    ]);
    $panel->tenantMenuItems($items);
    (new Unrelated)->userMenuItems([MenuItem::make()->label('Unrelated receiver')]);
}
