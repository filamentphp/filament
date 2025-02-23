<?php

use Filament\Auth\Http\Controllers\BlockEmailChangeVerificationController;
use Filament\Auth\Http\Controllers\EmailChangeVerificationController;
use Filament\Auth\Http\Controllers\EmailVerificationController;
use Filament\Auth\Http\Controllers\LogoutController;
use Filament\Facades\Filament;
use Filament\Http\Controllers\RedirectToHomeController;
use Filament\Http\Controllers\RedirectToTenantController;
use Filament\Panel;
use Illuminate\Support\Facades\Route;

Route::name('filament.')
    ->group(function (): void {
        foreach (Filament::getPanels() as $panel) {
            /** @var Panel $panel */
            $panelId = $panel->getId();
            $hasTenancy = $panel->hasTenancy();
            $tenantRoutePrefix = $panel->getTenantRoutePrefix();
            $tenantDomain = $panel->getTenantDomain();
            $tenantSlugAttribute = $panel->getTenantSlugAttribute();
            $domains = $panel->getDomains();

            foreach ((empty($domains) ? [null] : $domains) as $domain) {
                Route::domain($domain)
                    ->middleware($panel->getMiddleware())
                    ->name("{$panelId}." . ((filled($domain) && (count($domains) > 1)) ? "{$domain}." : ''))
                    ->prefix($panel->getPath())
                    ->group(function () use ($panel, $hasTenancy, $tenantDomain, $tenantRoutePrefix, $tenantSlugAttribute): void {
                        foreach ($panel->getRoutes() as $routes) {
                            $routes($panel);
                        }

                        Route::name('auth.')->group(function () use ($panel): void {
                            if ($panel->hasLogin()) {
                                Route::get($panel->getLoginRouteSlug(), $panel->getLoginRouteAction())
                                    ->name('login');
                            }

                            if ($panel->hasPasswordReset()) {
                                Route::name('password-reset.')
                                    ->prefix($panel->getResetPasswordRoutePrefix())
                                    ->group(function () use ($panel): void {
                                        Route::get($panel->getRequestPasswordResetRouteSlug(), $panel->getRequestPasswordResetRouteAction())
                                            ->name('request');
                                        Route::get($panel->getResetPasswordRouteSlug(), $panel->getResetPasswordRouteAction())
                                            ->middleware(['signed'])
                                            ->name('reset');
                                    });
                            }

                            if ($panel->hasRegistration()) {
                                Route::get($panel->getRegistrationRouteSlug(), $panel->getRegistrationRouteAction())
                                    ->name('register');
                            }
                        });

                        Route::middleware($panel->getAuthMiddleware())
                            ->group(function () use ($panel, $hasTenancy, $tenantDomain, $tenantRoutePrefix, $tenantSlugAttribute): void {
                                foreach ($panel->getAuthenticatedRoutes() as $routes) {
                                    $routes($panel);
                                }

                                Route::name('auth.')
                                    ->group(function () use ($panel): void {
                                        Route::post('/logout', LogoutController::class)->name('logout');

                                        if ($panel->hasProfile()) {
                                            $panel->getProfilePage()::registerRoutes($panel);
                                        }
                                    });

                                if ($panel->hasEmailVerification()) {
                                    Route::name('auth.email-verification.')
                                        ->prefix($panel->getEmailVerificationRoutePrefix())
                                        ->group(function () use ($panel): void {
                                            Route::get($panel->getEmailVerificationPromptRouteSlug(), $panel->getEmailVerificationPromptRouteAction())
                                                ->name('prompt');
                                            Route::get($panel->getEmailVerificationRouteSlug('/{id}/{hash}'), EmailVerificationController::class)
                                                ->middleware(['signed', 'throttle:6,1'])
                                                ->name('verify');
                                        });
                                }

                                if ($panel->hasEmailChangeVerification()) {
                                    Route::name('auth.email-change-verification.')
                                        ->prefix($panel->getEmailChangeVerificationRoutePrefix())
                                        ->group(function () use ($panel): void {
                                            Route::get($panel->getEmailChangeVerificationRouteSlug('/{id}/{email}'), EmailChangeVerificationController::class)
                                                ->middleware(['signed', 'throttle:6,1'])
                                                ->name('verify');

                                            Route::get($panel->getEmailChangeVerificationRouteSlug('/{id}/{email}/block'), BlockEmailChangeVerificationController::class)
                                                ->middleware(['signed', 'throttle:6,1'])
                                                ->name('block-verification');
                                        });
                                }

                                if ($panel->hasMultiFactorAuthentication()) {
                                    Route::name('auth.multi-factor-authentication.')
                                        ->prefix($panel->getMultiFactorAuthenticationRoutePrefix())
                                        ->group(function () use ($panel): void {
                                            if ($panel->isMultiFactorAuthenticationRequired()) {
                                                Route::get($panel->getSetUpRequiredMultiFactorAuthenticationRouteSlug(), $panel->getSetUpRequiredMultiFactorAuthenticationRouteAction())
                                                    ->name('set-up-required');
                                            }
                                        });
                                }

                                Route::name('tenant.')
                                    ->group(function () use ($panel): void {
                                        if ($panel->hasTenantRegistration()) {
                                            $panel->getTenantRegistrationPage()::registerRoutes($panel);
                                        }
                                    });

                                $routeGroup = Route::middleware($hasTenancy ? $panel->getTenantMiddleware() : []);

                                if (filled($tenantDomain)) {
                                    $routeGroup->domain($tenantDomain);
                                } else {
                                    $routeGroup->prefix(
                                        ($hasTenancy && blank($tenantDomain)) ?
                                            (
                                                filled($tenantRoutePrefix) ?
                                                    "{$tenantRoutePrefix}/" :
                                                    ''
                                            ) . ('{tenant' . (
                                                filled($tenantSlugAttribute) ?
                                                    ":{$tenantSlugAttribute}" :
                                                    ''
                                            ) . '}') :
                                            '',
                                    );
                                }

                                $routeGroup
                                    ->group(function () use ($panel): void {
                                        foreach ($panel->getAuthenticatedTenantRoutes() as $routes) {
                                            $routes($panel);
                                        }

                                        Route::get('/', RedirectToHomeController::class)->name('home');

                                        Route::name('tenant.')->group(function () use ($panel): void {
                                            if ($panel->hasTenantBilling()) {
                                                Route::get($panel->getTenantBillingRouteSlug(), $panel->getTenantBillingProvider()->getRouteAction())
                                                    ->name('billing');
                                            }

                                            if ($panel->hasTenantProfile()) {
                                                $panel->getTenantProfilePage()::registerRoutes($panel);
                                            }
                                        });

                                        foreach ($panel->getPages() as $page) {
                                            $page::registerRoutes($panel);
                                        }

                                        foreach ($panel->getResources() as $resource) {
                                            $resource::registerRoutes($panel);
                                        }
                                    });

                                if ($hasTenancy) {
                                    Route::get('/', RedirectToTenantController::class)->name('tenant');
                                }
                            });

                        if ($hasTenancy) {
                            $routeGroup = Route::middleware($panel->getTenantMiddleware());

                            if (filled($tenantDomain)) {
                                $routeGroup->domain($tenantDomain);
                            } else {
                                $routeGroup->prefix(
                                    (
                                        filled($tenantRoutePrefix) ?
                                            "{$tenantRoutePrefix}/" :
                                            ''
                                    ) . '{tenant' . (
                                        filled($tenantSlugAttribute) ?
                                            ":{$tenantSlugAttribute}" :
                                            ''
                                    ) . '}',
                                );
                            }

                            $routeGroup
                                ->group(function () use ($panel): void {
                                    foreach ($panel->getTenantRoutes() as $routes) {
                                        $routes($panel);
                                    }
                                });
                        }
                    });
            }
        }
    });
