<?php

namespace App\Livewire\Panels\Navigation;

use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ChangeIcon extends Page
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
                        ->url('#'),
                    NavigationItem::make('Posts')
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->isActiveWhen(static fn () => true)
                        ->url('#'),
                    NavigationItem::make('Orders')
                        ->icon(Heroicon::OutlinedShoppingCart)
                        ->url('#'),
                ]);
            });
    }
}
