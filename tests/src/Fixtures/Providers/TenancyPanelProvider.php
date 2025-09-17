<?php

namespace Filament\Tests\Fixtures\Providers;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Http\Middleware\IdentifyTenant;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Pages\Actions;
use Filament\Tests\Fixtures\Pages\Settings;
use Filament\Tests\Fixtures\Resources\PostCategories\PostCategoryResource;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Fixtures\Resources\Shop\Products\ProductResource;
use Filament\Tests\Fixtures\Resources\Users\UserResource;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class TenancyPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('tenancy')
            ->path('tenancy')
            ->tenant(Team::class)
            ->login()
            ->registration()
            ->passwordReset()
            ->emailVerification()
            ->resources([
                PostResource::class,
                PostCategoryResource::class,
                ProductResource::class,
                UserResource::class,
            ])
            ->pages([
                Pages\Dashboard::class,
                Actions::class,
                Settings::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
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
