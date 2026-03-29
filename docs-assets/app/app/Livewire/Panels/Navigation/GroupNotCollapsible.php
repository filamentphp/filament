<?php

namespace App\Livewire\Panels\Navigation;

use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class GroupNotCollapsible extends Page
{
    protected string $view = 'livewire.panels.navigation.empty';

    public function mount()
    {
        filament()
            ->getCurrentPanel()
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder
                    ->items([
                        NavigationItem::make('Dashboard')
                            ->icon(Heroicon::OutlinedHome)
                            ->isActiveWhen(static fn () => true)
                            ->url('#'),
                    ])
                    ->groups([
                        NavigationGroup::make('Shop')
                            ->items([
                                NavigationItem::make('Products')
                                    ->icon(Heroicon::OutlinedShoppingBag)
                                    ->url('#'),
                                NavigationItem::make('Orders')
                                    ->icon(Heroicon::OutlinedShoppingCart)
                                    ->url('#'),
                            ]),
                        NavigationGroup::make('Settings')
                            ->collapsible(false)
                            ->items([
                                NavigationItem::make('General')
                                    ->icon(Heroicon::OutlinedCog6Tooth)
                                    ->url('#'),
                                NavigationItem::make('Integrations')
                                    ->icon(Heroicon::OutlinedBolt)
                                    ->url('#'),
                            ]),
                    ]);
            });
    }
}
