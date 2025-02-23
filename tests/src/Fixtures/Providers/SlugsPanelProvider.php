<?php

namespace Filament\Tests\Fixtures\Providers;

use Filament\Auth\MultiFactor\EmailCode\EmailCodeAuthentication;
use Filament\Auth\MultiFactor\GoogleTwoFactor\GoogleTwoFactorAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
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
            ->loginRouteSlug('login-test')
            ->passwordReset()
            ->passwordResetRequestRouteSlug('request-test')
            ->passwordResetRouteSlug('reset-test')
            ->passwordResetRoutePrefix('password-reset-test')
            ->registration()
            ->registrationRouteSlug('register-test')
            ->emailChangeVerification()
            ->emailChangeVerificationRouteSlug('verify-change-test')
            ->emailChangeVerificationRoutePrefix('email-change-verification-test')
            ->emailVerification()
            ->emailVerificationPromptRouteSlug('prompt-test')
            ->emailVerificationRouteSlug('verify-test')
            ->emailVerificationRoutePrefix('email-verification-test')
            ->multiFactorAuthentication([
                EmailCodeAuthentication::make(),
                GoogleTwoFactorAuthentication::make(),
            ], isRequired: true)
            ->setUpRequiredMultiFactorAuthenticationRouteSlug('set-up-test')
            ->multiFactorAuthenticationRoutePrefix('multi-factor-authentication-test')
            ->profile()
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
