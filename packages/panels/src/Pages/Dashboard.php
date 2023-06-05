<?php

namespace Filament\Pages;

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Route;

class Dashboard extends Page
{
    protected static ?int $navigationSort = -2;

    /**
     * @var view-string
     */
    protected static string $view = 'filament::pages.dashboard';

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ??
            static::$title ??
            __('filament::pages/dashboard.title');
    }

    public static function getNavigationIcon(): string
    {
        return static::$navigationIcon ??
            FilamentIcon::resolve('panels::pages.dashboard.navigation')?->name ??
            'heroicon-o-home';
    }

    public static function routes(Panel $panel): void
    {
        Route::get('/', static::class)
            ->middleware(static::getRouteMiddleware($panel))
            ->name(static::getSlug());
    }

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return Filament::getWidgets();
    }

    /**
     * @return int | string | array<string, int | string | null>
     */
    public function getColumns(): int | string | array
    {
        return 2;
    }

    public function getTitle(): string | Htmlable
    {
        return static::$title ?? __('filament::pages/dashboard.title');
    }
}
