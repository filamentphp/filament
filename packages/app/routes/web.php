<?php

use Filament\Facades\Filament;
use Filament\Http\Middleware\SetUpContext;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::domain(config('filament.domain'))
    ->middleware(config('filament.middleware.base'))
    ->name('filament.')
    ->group(function () {
        Route::prefix(config('filament.core_path'))->group(function () {
            Route::post('/logout', function (Request $request): LogoutResponse {
                Filament::auth()->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return app(LogoutResponse::class);
            })->name('auth.logout');
        });

        foreach (Filament::getContexts() as $context) {
            $contextId = $context->getId();

            Route::middleware(["context:{$contextId}"])
                ->name(($contextId !== 'default') ? "{$contextId}." : '')
                ->prefix($context->getPath())
                ->group(function () use ($context) {
                    if ($login = $context->getLogin()) {
                        Route::get('/login', $login)->name('auth.login');
                    }

                    Route::middleware(config('filament.middleware.auth'))->group(function () use ($context): void {
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
