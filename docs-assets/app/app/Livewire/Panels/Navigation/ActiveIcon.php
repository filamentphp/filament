<?php

namespace App\Livewire\Panels\Navigation;

use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ActiveIcon extends Page
{
    protected string $view = 'livewire.panels.navigation.empty';

    public function mount()
    {
        filament()
            ->getCurrentPanel()
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->items([
                    NavigationItem::make('Dashboard')
                        ->icon(Heroicon::OutlinedHome)
                        ->activeIcon(Heroicon::Home)
                        ->isActiveWhen(static fn () => true)
                        ->url('#'),
                    NavigationItem::make('Orders')
                        ->icon(Heroicon::OutlinedShoppingCart)
                        ->activeIcon(Heroicon::ShoppingCart)
                        ->url('#'),
                    NavigationItem::make('Products')
                        ->icon(Heroicon::OutlinedShoppingBag)
                        ->activeIcon(Heroicon::ShoppingBag)
                        ->url('#'),
                ]);
            });
    }
}
