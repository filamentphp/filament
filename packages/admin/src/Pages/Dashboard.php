<?php

namespace Filament\Pages;

use Closure;
use Illuminate\Support\Facades\Route;

class Dashboard extends Page
{
    public static ?string $navigationIcon = 'heroicon-o-home';

    public static ?int $navigationSort = -2;

    public static string $view = 'filament::dashboard';

    public static function getRoutes(): Closure
    {
        return function () {
            Route::get('/', static::class)->name(static::getSlug());
        };
    }
}
