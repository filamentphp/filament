<?php

namespace App\Livewire\Panels\Navigation;

use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class BadgeColor extends Page
{
    protected string $view = 'livewire.panels.navigation.empty';

    public function mount()
    {
        filament()
            ->getCurrentPanel()
            ->navigationItems([
                NavigationItem::make()
                    ->label('Orders')
                    ->url(fn (): string => '#')
                    ->icon(Heroicon::OutlinedShoppingCart)
                    ->badge(32, 'danger'),
            ]);
    }
}
