<?php

use Filament\Billing\Providers\Contracts\BillingProvider;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Mechanisms\PersistentMiddleware\PersistentMiddleware;

class HasMiddlewareTestCase extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app->register(new class($app) extends ServiceProvider
        {
            public function register(): void
            {
                Filament::registerPanel(fn (): Panel => Panel::make()
                    ->id('boot-registered-middleware')
                    ->multiFactorAuthenticationRequiredMiddlewareName('boot-registered-required-mfa'));
            }

            public function boot(): void
            {
                Route::aliasMiddleware('boot-registered-required-mfa', 'App\\Http\\Middleware\\BootRegisteredRequiredMultiFactorAuthentication');
            }
        });
    }
}

uses(HasMiddlewareTestCase::class);

it('registers configured page access middleware as Livewire persistent middleware', function (): void {
    Route::aliasMiddleware('custom-verified', 'App\\Http\\Middleware\\CustomEnsureEmailIsVerified');

    Panel::make()
        ->id('custom-page-access-middleware')
        ->emailVerifiedMiddlewareName('custom-verified')
        ->multiFactorAuthenticationRequiredMiddlewareName('App\\Http\\Middleware\\CustomEnsureMultiFactorAuthenticationIsEnabled')
        ->tenantBillingProvider(new class implements BillingProvider
        {
            public function getRouteAction(): string
            {
                return 'App\\Http\\Controllers\\BillingController';
            }

            public function getSubscribedMiddleware(): string
            {
                return 'App\\Http\\Middleware\\CustomVerifyBillableIsSubscribed:premium';
            }
        })
        ->register();

    expect(app(PersistentMiddleware::class)->getPersistentMiddleware())
        ->toContain('App\\Http\\Middleware\\CustomEnsureEmailIsVerified')
        ->toContain('App\\Http\\Middleware\\CustomEnsureMultiFactorAuthenticationIsEnabled')
        ->toContain('App\\Http\\Middleware\\CustomVerifyBillableIsSubscribed');
});

it('registers multi-domain panels in the console without a current domain', function (): void {
    $environment = app()->environment();
    app()->instance('env', 'production');

    try {
        expect(app()->runningInConsole())->toBeTrue();

        expect(fn () => Panel::make()
            ->id('console-multi-domain')
            ->domains(['admin.example.com', 'admin.example.org'])
            ->register())->not->toThrow(LogicException::class);
    } finally {
        app()->instance('env', $environment);
    }
});

it('resolves persistent middleware aliases registered during application provider boot', function (): void {
    expect(app(PersistentMiddleware::class)->getPersistentMiddleware())
        ->toContain('App\\Http\\Middleware\\BootRegisteredRequiredMultiFactorAuthentication');
});
