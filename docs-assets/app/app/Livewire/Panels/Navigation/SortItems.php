<?php

namespace App\Livewire\Panels\Navigation;

use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SortItems extends Page
{
    protected string $view = 'livewire.panels.navigation.empty';

    public function mount()
    {
        filament()
            ->getCurrentPanel()
            ->navigationItems([
                NavigationItem::make()
                    ->label('Products')
                    ->badge('2')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->sort(2)
                    ->url(fn (): string => '#'),
                NavigationItem::make('')
                    ->label('Orders')
                    ->badge('1')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->sort(1)
                    ->url(fn (): string => '#'),
            ]);
    }
}
