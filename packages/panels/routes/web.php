<?php

use Filament\Facades\Filament;
use Filament\Http\Controllers\Auth\EmailVerificationController;
use Filament\Http\Controllers\Auth\LogoutController;
use Filament\Http\Controllers\RedirectToHomeController;
use Filament\Http\Controllers\RedirectToTenantController;
use Illuminate\Support\Facades\Route;

Route::name('filament.')
    ->group(function () {
        foreach (Filament::getPanels() as $panel) {
            /** @var \Filament\Panel $panel */
            $panelId = $panel->getId();
            $hasTenancy = $panel->hasTenancy();
            $tenantRoutePrefix = $panel->getTenantRoutePrefix();
            $tenantSlugAttribute = $panel->getTenantSlugAttribute();
            $domains = $panel->getDomains();

            foreach ((empty($domains) ? [null] : $domains) as $domain) {
                Route::domain($domain)
                    ->middleware($panel->getMiddleware())
                    ->name("{$panelId}.")
                    ->prefix($panel->getPath())
                    ->group(function () use ($panel, $hasTenancy, $tenantRoutePrefix, $tenantSlugAttribute) {
                        if ($routes = $panel->getRoutes()) {
                            $routes($panel);
                        }

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
                        });

                        Route::middleware($panel->getAuthMiddleware())
                            ->group(function () use ($panel, $hasTenancy, $tenantRoutePrefix, $tenantSlugAttribute): void {
                                if ($routes = $panel->getAuthenticatedRoutes()) {
                                    $routes($panel);
                                }

                                if ($hasTenancy) {
                                    Route::get('/', RedirectToTenantController::class)->name('tenant');
                                }

                                Route::name('auth.')
                                    ->group(function () use ($panel): void {
                                        Route::post('/logout', LogoutController::class)->name('logout');

                                        if ($panel->hasProfile()) {
                                            $panel->getProfilePage()::routes($panel);
                                        }
                                    });

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
                                    ->group(function () use ($panel): void {
                                        if ($panel->hasTenantRegistration()) {
                                            $panel->getTenantRegistrationPage()::routes($panel);
                                        }
                                    });

                                Route::middleware($hasTenancy ? $panel->getTenantMiddleware() : [])
                                    ->prefix($hasTenancy ? (($tenantRoutePrefix) ? "{$tenantRoutePrefix}/" : '') . ('{tenant' . (($tenantSlugAttribute) ? ":{$tenantSlugAttribute}" : '') . '}') : '')
                                    ->group(function () use ($panel): void {
                                        if ($routes = $panel->getAuthenticatedTenantRoutes()) {
                                            $routes($panel);
                                        }

                                        Route::get('/', RedirectToHomeController::class)->name('home');

                                        Route::name('tenant.')->group(function () use ($panel): void {
                                            if ($panel->hasTenantBilling()) {
                                                Route::get('/billing', $panel->getTenantBillingProvider()->getRouteAction())
                                                    ->name('billing');
                                            }

                                            if ($panel->hasTenantProfile()) {
                                                $panel->getTenantProfilePage()::routes($panel);
                                            }
                                        });

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
                                    });

                            });

                        if ($hasTenancy) {
                            Route::middleware($panel->getTenantMiddleware())
                                ->prefix((($tenantRoutePrefix) ? "{$tenantRoutePrefix}/" : '') . '{tenant' . (($tenantSlugAttribute) ? ":{$tenantSlugAttribute}" : '') . '}')
                                ->group(function () use ($panel): void {
                                    if ($routes = $panel->getTenantRoutes()) {
                                        $routes($panel);
                                    }
                                });
                        }
                    });
            }
        }
    });
