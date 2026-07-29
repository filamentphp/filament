<?php

namespace Filament\Tests\Fixtures\Providers;

use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Notifications\Notification;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class UserMenuGroupingPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('user-menu-grouping')
            ->path('user-menu-grouping')
            ->login()
            ->pages([
                Pages\Dashboard::class,
            ])
            ->userMenuItems([
                [
                    Action::make('alpha')
                        ->action(fn () => Notification::make()->title('alpha ran')->send())
                        ->sort(1),
                    Action::make('beta')
                        ->action(fn () => Notification::make()->title('beta ran')->send())
                        ->sort(2),
                ],
                [
                    Action::make('gamma')
                        ->action(fn () => Notification::make()->title('gamma ran')->send())
                        ->sort(10),
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
