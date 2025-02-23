<?php

namespace App\Livewire\Panels\Navigation;

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
            ->navigationItems([
                NavigationItem::make()
                    ->label('Settings Inactive')
                    ->url(fn (): string => '#')
                    ->activeIcon(Heroicon::OutlinedDocumentText)
                    ->icon(Heroicon::OutlinedCog),
                NavigationItem::make('')
                    ->label('Settings Active')
                    ->url(fn (): string => '#')
                    ->isActiveWhen(fn () => request()->path() === 'panels/navigation/active-icon')
                    ->activeIcon(Heroicon::Cog)
                    ->icon(Heroicon::Cog),
            ]);
    }
}
