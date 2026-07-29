<?php

namespace Filament\Tests\Fixtures\Providers;

use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Http\Middleware\IdentifyTenant;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Pages\Tenancy\RegisterTeam;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class TenantMenuRegisterPlacementPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('tenant-menu-register-placement')
            ->path('tenant-menu-register-placement')
            ->tenant(Team::class)
            ->tenantRegistration(RegisterTeam::class)
            ->login()
            ->pages([
                Pages\Dashboard::class,
            ])
            ->tenantMenuItems([
                [
                    Action::make('settings'),
                    'register' => fn (Action $action): Action => $action,
                ],
                [
                    Action::make('help'),
                ],
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                class_exists(PreventRequestForgery::class) ? PreventRequestForgery::class : VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                IdentifyTenant::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
