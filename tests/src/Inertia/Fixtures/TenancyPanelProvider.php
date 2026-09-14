<?php

namespace Filament\Tests\Inertia\Fixtures;

use Filament\Http\Middleware\Authenticate;
use Filament\Inertia\InertiaPlugin;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Resources\Tenancy\TenantScopedUsers\TenantScopedUserResource;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class TenancyPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('inertia-tenancy-tests')
            ->path('integration-tenancy')
            ->authGuard('panel')
            ->login()
            ->tenant(Team::class)
            ->plugin(InertiaPlugin::make()->renderer('/build/fixture-renderer.js')->middleware(HandleInertiaRequests::class))
            ->pages([TestPage::class])
            ->resources([TenantScopedUserResource::class])
            ->middleware([EncryptCookies::class, AddQueuedCookiesToResponse::class, StartSession::class, ShareErrorsFromSession::class])
            ->authMiddleware([Authenticate::class]);
    }
}
