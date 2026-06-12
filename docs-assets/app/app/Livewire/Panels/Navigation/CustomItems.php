<?php

namespace App\Livewire\Panels\Navigation;

use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class CustomItems extends Page
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
                        ->isActiveWhen(static fn () => true)
                        ->url('#'),
                    NavigationItem::make('Users')
                        ->icon(Heroicon::OutlinedUserGroup)
                        ->url('#'),
                    NavigationItem::make('Settings')
                        ->icon(Heroicon::OutlinedCog6Tooth)
                        ->url('#'),
                ]);
            });
    }
}
