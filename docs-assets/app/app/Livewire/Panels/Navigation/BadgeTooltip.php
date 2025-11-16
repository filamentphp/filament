<?php

namespace App\Livewire\Panels\Navigation;

use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class BadgeTooltip extends Page
{
    protected string $view = 'livewire.panels.navigation.empty';

    public function mount()
    {
        filament()
            ->getCurrentPanel()
            ->navigationItems([
                NavigationItem::make()
                    ->label('Users')
                    ->badge(12)
                    ->badgeTooltip('The number of users')
                    ->icon(Heroicon::OutlinedUserGroup)
                    ->url(fn (): string => '#'),
            ]);
    }
}
