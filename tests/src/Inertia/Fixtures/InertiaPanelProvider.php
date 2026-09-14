<?php

namespace Filament\Tests\Inertia\Fixtures;

use Filament\Http\Middleware\Authenticate;
use Filament\Inertia\InertiaPlugin;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class InertiaPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel->id('inertia-tests')->path('integration')->login()
            ->plugin(InertiaPlugin::make()->renderer(static fn (): string => '/build/fixture-renderer.js')->middleware(HandleInertiaRequests::class))
            ->pages([TestPage::class])
            ->authenticatedRoutes(static function (): void {
                Route::get('simple', TestSimplePage::class)->name('simple');
                Route::get('resource', UnsupportedResourcePage::class)->name('resource');
                Route::patch('draft', static function (Request $request) {
                    $request->validate(['title' => ['required', 'min:5']]);

                    return redirect('/integration/page');
                })->name('draft');
            })
            ->middleware([EncryptCookies::class, AddQueuedCookiesToResponse::class, StartSession::class, ShareErrorsFromSession::class])
            ->authMiddleware([Authenticate::class]);
    }
}
