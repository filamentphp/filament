<?php

namespace Filament\Pages;

use Filament\Context;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentIcon;
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
            FilamentIcon::resolve('app::pages.dashboard.navigation')?->name ??
            'heroicon-o-home';
    }

    public static function routes(Context $context): void
    {
        Route::get('/', static::class)
            ->middleware(static::getRouteMiddleware($context))
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
     * @return int | array<string, int | null>
     */
    public function getColumns(): int | array
    {
        return 2;
    }

    public function getTitle(): string
    {
        return static::$title ?? __('filament::pages/dashboard.title');
    }
}
