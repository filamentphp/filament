<?php

namespace App\Livewire\Panels\Navigation;

use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Badge extends Page
{
    protected string $view = 'livewire.panels.navigation.empty';

    public function mount()
    {
        filament()
            ->getCurrentPanel()
            ->navigationItems([
                NavigationItem::make()
                    ->label('Orders')
                    ->badge(24)
                    ->icon(Heroicon::OutlinedShoppingCart)
                    ->url(fn (): string => '#'),
            ]);
    }
}
