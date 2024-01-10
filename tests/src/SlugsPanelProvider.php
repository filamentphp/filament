<?php

namespace Filament\Tests;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SlugsPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('slugs')
            ->path('slugs')
            ->login()
            ->loginSlug('login-test')
            ->passwordReset()
            ->passwordResetRequestSlug('request-test')
            ->passwordResetSlug('reset-test')
            ->passwordResetPrefix('password-reset-test')
            ->registration()
            ->registrationSlug('register-test')
            ->emailVerification()
            ->emailVerificationPromptSlug('prompt-test')
            ->emailVerificationVerifySlug('verify-test')
            ->emailVerificationPrefix('email-verification-test')
            ->resources([])
            ->pages([])
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
