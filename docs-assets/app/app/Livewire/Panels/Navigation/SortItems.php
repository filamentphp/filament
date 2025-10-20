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
                    ->sort(2)
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->url(fn (): string => '#'),
                NavigationItem::make('')
                    ->label('Orders')
                    ->badge('1')
                    ->sort(1)
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->url(fn (): string => '#'),
            ]);
    }
}
