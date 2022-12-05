<?php

namespace Filament\Tests;

use Filament\Context;
use Filament\ContextProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Http\Middleware\MirrorConfigToSubpackages;
use Filament\Pages;
use Filament\Tests\Actions\Fixtures\Pages\Actions;
use Filament\Tests\App\Fixtures\Pages\Settings;
use Filament\Tests\App\Fixtures\Resources\PostCategoryResource;
use Filament\Tests\App\Fixtures\Resources\PostResource;
use Filament\Tests\App\Fixtures\Resources\ProductResource;
use Filament\Tests\App\Fixtures\Resources\UserResource;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminContextProvider extends ContextProvider
{
    public function context(Context $context): Context
    {
        return $context
            ->default()
            ->id('admin')
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
                DispatchServingFilamentEvent::class,
                MirrorConfigToSubpackages::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
