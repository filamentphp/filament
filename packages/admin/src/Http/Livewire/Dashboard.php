<?php

namespace Filament\Http\Livewire;

use Filament\Pages\Page;

class Dashboard extends Page
{
    public static ?string $navigationIcon = 'heroicon-o-home';

    public static ?int $navigationSort = -2;

    public static ?string $routeName = 'filament.dashboard';

    public static string $view = 'filament::dashboard';
}
