<?php

namespace Filament\Pages;

class Dashboard extends Page
{
    public static ?string $navigationIcon = 'heroicon-o-home';

    public static ?int $navigationSort = -2;

    public static string $view = 'filament::dashboard';
}
