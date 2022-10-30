<?php

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::name('filament.')
    ->group(function () {
        foreach (Filament::getContexts() as $context) {
            /** @var \Filament\Context $context */
            $contextId = $context->getId();

            Route::domain($context->getDomain())
                ->middleware($context->getMiddleware())
                ->name("{$contextId}.")
                ->prefix($context->getPath())
                ->group(function () use ($context) {
                    Route::name('auth.')->group(function () use ($context) {
                        if ($context->hasLogin()) {
                            Route::get('/login', $context->getLoginPage())->name('login');
                        }

                        Route::post('/logout', function (Request $request) use ($context): LogoutResponse {
                            $context->auth()->logout();

                            $request->session()->invalidate();
                            $request->session()->regenerateToken();

                            return app(LogoutResponse::class);
                        })->name('logout');
                    });

                    Route::middleware($context->getAuthMiddleware())
                        ->group(function () use ($context): void {
                            $hasRoutableTenancy = $context->hasRoutableTenancy();
                            $hasTenantRegistration = $context->hasTenantRegistration();
                            $tenantSlugField = $context->getTenantSlugField();

                            if ($hasRoutableTenancy) {
                                Route::get('/', function () use ($context, $hasTenantRegistration): RedirectResponse {
                                    $tenant = Filament::getUserDefaultTenant(Filament::auth()->user());

                                    if ($tenant) {
                                        return redirect($context->getUrl($tenant));
                                    }

                                    if (! $hasTenantRegistration) {
                                        abort(404);
                                    }

                                    return redirect($context->getTenantRegistrationUrl());
                                });
                            }

                            Route::name('tenant.')
                                ->group(function () use ($context, $hasTenantRegistration): void {
                                    if ($hasTenantRegistration) {
                                        Route::group([], Closure::fromCallable([$context->getTenantRegistrationPage(), 'routes']));
                                    }
                                });

                            Route::prefix($hasRoutableTenancy ? ('{tenant' . (($tenantSlugField) ? ":{$tenantSlugField}" : '') . '}') : '')
                                ->group(function () use ($context): void {
                                    Route::get('/', function () use ($context): RedirectResponse {
                                        return redirect($context->getUrl(Filament::getTenant()));
                                    });

                                    if ($context->hasTenantBilling()) {
                                        Route::get('/billing', $context->getTenantBillingProvider()->getRouteAction())
                                            ->name('tenant.billing');
                                    }

                                    Route::name('pages.')->group(function () use ($context): void {
                                        foreach ($context->getPages() as $page) {
                                            Route::group([], Closure::fromCallable([$page, 'routes']));
                                        }
                                    });

                                    Route::name('resources.')->group(function () use ($context): void {
                                        foreach ($context->getResources() as $resource) {
                                            Route::group([], Closure::fromCallable([$resource, 'routes']));
                                        }
                                    });

                                    Route::group([], Closure::fromCallable([$context, 'getAuthenticatedTenantRoutes']));
                                });

                            Route::group([], Closure::fromCallable([$context, 'getAuthenticatedRoutes']));
                        });

                    Route::group([], Closure::fromCallable([$context, 'getRoutes']));
                });
        }
    });
