<?php

use Filament\Facades\Filament;
use Filament\Http\Controllers\Auth\EmailVerificationController;
use Filament\Http\Controllers\Auth\LogoutController;
use Filament\Http\Middleware\IdentifyTenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

Route::name('filament.')
    ->group(function () {
        foreach (Filament::getPanels() as $panel) {
            /** @var \Filament\Panel $panel */
            $panelId = $panel->getId();
            $hasTenancy = $panel->hasTenancy();
            $hasTenantRegistration = $panel->hasTenantRegistration();
            $tenantSlugAttribute = $panel->getTenantSlugAttribute();

            Route::domain($panel->getDomain())
                ->middleware($panel->getMiddleware())
                ->name("{$panelId}.")
                ->prefix($panel->getPath())
                ->group(function () use ($panel, $hasTenancy, $hasTenantRegistration, $tenantSlugAttribute) {
                    Route::name('auth.')->group(function () use ($panel) {
                        if ($panel->hasLogin()) {
                            Route::get('/login', $panel->getLoginRouteAction())->name('login');
                        }

                        if ($panel->hasPasswordReset()) {
                            Route::name('password-reset.')
                                ->prefix('/password-reset')
                                ->group(function () use ($panel) {
                                    Route::get('/request', $panel->getRequestPasswordResetRouteAction())->name('request');
                                    Route::get('/reset', $panel->getResetPasswordRouteAction())
                                        ->middleware(['signed'])
                                        ->name('reset');
                                });
                        }

                        if ($panel->hasRegistration()) {
                            Route::get('/register', $panel->getRegistrationRouteAction())->name('register');
                        }

                        Route::post('/logout', LogoutController::class)->name('logout');
                    });

                    Route::middleware($panel->getAuthMiddleware())
                        ->group(function () use ($panel, $hasTenancy, $hasTenantRegistration, $tenantSlugAttribute): void {
                            if ($hasTenancy) {
                                Route::get('/', function () use ($panel, $hasTenantRegistration): RedirectResponse {
                                    $tenant = Filament::getUserDefaultTenant(Filament::auth()->user());

                                    if ($tenant) {
                                        $url = $panel->getUrl($tenant);
                                        abort_if(blank($url), 404);

                                        return redirect($url);
                                    }

                                    if (! $hasTenantRegistration) {
                                        abort(404);
                                    }

                                    return redirect($panel->getTenantRegistrationUrl());
                                })->name('home');
                            }

                            if ($panel->hasEmailVerification()) {
                                Route::name('auth.email-verification.')
                                    ->prefix('/email-verification')
                                    ->group(function () use ($panel) {
                                        Route::get('/prompt', $panel->getEmailVerificationPromptRouteAction())->name('prompt');
                                        Route::get('/verify', EmailVerificationController::class)
                                            ->middleware(['signed'])
                                            ->name('verify');
                                    });
                            }

                            Route::name('tenant.')
                                ->group(function () use ($panel, $hasTenantRegistration): void {
                                    if ($hasTenantRegistration) {
                                        $panel->getTenantRegistrationPage()::routes($panel);
                                    }
                                });

                            if ($routes = $panel->getAuthenticatedRoutes()) {
                                $routes($panel);
                            }

                            Route::middleware($hasTenancy ? [IdentifyTenant::class] : [])
                                ->prefix($hasTenancy ? ('{tenant' . (($tenantSlugAttribute) ? ":{$tenantSlugAttribute}" : '') . '}') : '')
                                ->group(function () use ($panel): void {
                                    Route::get('/', function () use ($panel): RedirectResponse {
                                        $url = $panel->getUrl(Filament::getTenant());
                                        abort_if(blank($url), 404);

                                        return redirect($url);
                                    })->name('tenant');

                                    if ($panel->hasTenantBilling()) {
                                        Route::get('/billing', $panel->getTenantBillingProvider()->getRouteAction())
                                            ->name('tenant.billing');
                                    }

                                    Route::name('pages.')->group(function () use ($panel): void {
                                        foreach ($panel->getPages() as $page) {
                                            $page::routes($panel);
                                        }
                                    });

                                    Route::name('resources.')->group(function () use ($panel): void {
                                        foreach ($panel->getResources() as $resource) {
                                            $resource::routes($panel);
                                        }
                                    });

                                    if ($routes = $panel->getAuthenticatedTenantRoutes()) {
                                        $routes($panel);
                                    }
                                });

                        });

                    if ($hasTenancy) {
                        Route::middleware([IdentifyTenant::class])
                            ->prefix('{tenant' . (($tenantSlugAttribute) ? ":{$tenantSlugAttribute}" : '') . '}')
                            ->group(function () use ($panel): void {
                                if ($routes = $panel->getTenantRoutes()) {
                                    $routes($panel);
                                }
                            });
                    }

                    if ($routes = $panel->getRoutes()) {
                        $routes($panel);
                    }
                });
        }
    });
