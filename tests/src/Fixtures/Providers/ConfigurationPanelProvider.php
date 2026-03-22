<?php

namespace Filament\Tests\Fixtures\Providers;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Tests\Fixtures\Pages\ConfigurableSettings;
use Filament\Tests\Fixtures\Resources\Posts\ConfigurablePostResource;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ConfigurationPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('configuration')
            ->path('configuration')
            ->login()
            ->resources([
                // Default resource (no configuration)
                ConfigurablePostResource::class,
                // Featured posts configuration
                ConfigurablePostResource::make('featured')
                    ->slug('featured-posts')
                    ->navigationLabel('Featured Posts')
                    ->navigationGroup('Featured Content')
                    ->navigationSort(1)
                    ->featured(),
                // Archived posts configuration
                ConfigurablePostResource::make('archived')
                    ->slug('archived-posts')
                    ->navigationLabel('Archived Posts')
                    ->navigationGroup('Archive')
                    ->navigationSort(100)
                    ->archived(),
            ])
            ->pages([
                Pages\Dashboard::class,
                // Default page (no configuration)
                ConfigurableSettings::class,
                // General settings configuration
                ConfigurableSettings::make('general')
                    ->slug('general-settings')
                    ->navigationLabel('General Settings')
                    ->navigationGroup('Settings')
                    ->navigationSort(1)
                    ->settingsCategory('general'),
                // Advanced settings configuration
                ConfigurableSettings::make('advanced')
                    ->slug('advanced-settings')
                    ->navigationLabel('Advanced Settings')
                    ->navigationGroup('Settings')
                    ->navigationSort(2)
                    ->settingsCategory('advanced'),
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
