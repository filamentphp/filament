<?php

namespace App\Livewire\Panels\Navigation;

use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class TopNavigation extends Page
{
    protected string $view = 'livewire.panels.navigation.empty';

    public function mount()
    {
        filament()
            ->getCurrentPanel()
            ->navigationItems([
                NavigationItem::make('Analytics')
                    ->url('https://filament.pirsch.io', shouldOpenInNewTab: true)
                    ->icon(Heroicon::OutlinedPresentationChartLine)
                    ->group('Reports')
                    ->sort(3),
                NavigationItem::make('dashboard')
                    ->label(fn (): string => __('filament-panels::pages/dashboard.title'))
                    ->url(fn (): string => Dashboard::getUrl())
                    ->isActiveWhen(fn () => request()->routeIs('filament.admin.pages.dashboard')),
            ])
            ->topNavigation();
    }
}
