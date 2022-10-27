<?php

use Filament\Context;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(config('filament.middleware.base'))
    ->name('filament.')
    ->group(function () {
        foreach (Filament::getContexts() as $context) {
            /** @var \Filament\Context $context */

            $contextId = $context->getId();

            Route::domain($context->getDomain())
                ->middleware(["context:{$contextId}"])
                ->name("{$contextId}.")
                ->prefix($context->getPath())
                ->group(function () use ($context) {
                    Route::name('auth.')->group(function () use ($context) {
                        if ($context->hasLogin()) {
                            Route::get('/login', $context->getLogin())->name('login');
                        }

                        Route::post('/logout', function (Request $request) use ($context): LogoutResponse {
                            $context->auth()->logout();

                            $request->session()->invalidate();
                            $request->session()->regenerateToken();

                            return app(LogoutResponse::class);
                        })->name('logout');
                    });

                    $hasTenancy = $context->hasTenancy();
                    $tenantSlugField = $context->getTenantSlugField();

                    Route::middleware(config('filament.middleware.auth'))
                        ->prefix($hasTenancy ? ('{tenant' . (($tenantSlugField) ? ":{$tenantSlugField}" : '') . '}') : '')
                        ->group(function () use ($context): void {
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
                        });
                });
        }
    });
