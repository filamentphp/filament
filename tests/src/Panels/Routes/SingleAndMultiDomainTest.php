<?php

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\PanelRegistry;
use Filament\Tests\Panels\Pages\TestCase;
use Illuminate\Support\Facades\Route;

uses(TestCase::class);

describe('domain-qualified route names', function (): void {
    it('panels without any domain should not use the domain in the names of routes', function (): void {
        $routeName = 'filament.admin.auth.login';
        $route = Route::getRoutes()->getByName($routeName);
        expect($route)->not->toBeEmpty();
    });

    it('panels with a single domain should not use the domain in the names of routes', function (): void {
        $routeName = 'filament.single-domain.auth.login';
        $route = Route::getRoutes()->getByName($routeName);
        expect($route)->not->toBeEmpty();

        $routeName = 'filament.single-domain.example3.com.auth.login';
        $route = Route::getRoutes()->getByName($routeName);
        expect($route)->toBeEmpty();
    });

    it('panels with multiple domains should use the domain in names of all routes', function (): void {
        $routeName = 'filament.multi-domain.auth.login';
        $route = Route::getRoutes()->getByName($routeName);
        expect($route)->toBeEmpty();

        $routeName = 'filament.multi-domain.example.com.auth.login';
        $route = Route::getRoutes()->getByName($routeName);
        expect($route)->not->toBeEmpty();

        $routeName = 'filament.multi-domain.example2.com.auth.login';
        $route = Route::getRoutes()->getByName($routeName);
        expect($route)->not->toBeEmpty();
    });

    it('sets and clears the domain used to generate URLs through either context setter', function (string $method): void {
        Filament::setCurrentPanel('multi-domain');

        expect(Filament::{$method}(domain: 'example2.com'))->toBeNull()
            ->and(filament()->getCurrentDomain())->toBe('example2.com')
            ->and(Filament::getLoginUrl())->toBe('http://example2.com/login');

        expect(filament()->{$method}(domain: 'example.com'))->toBeNull()
            ->and(Filament::getCurrentDomain())->toBe('example.com')
            ->and(Filament::getLoginUrl())->toBe('http://example.com/login');

        expect(Filament::{$method}(domain: null))->toBeNull()
            ->and(Filament::getCurrentDomain(testingDomain: 'fallback.example.com'))->toBe('fallback.example.com')
            ->and(Filament::getLoginUrl())->toBe('http://example.com/login');
    })->with(['setCurrentDomain', 'currentDomain']);

    it('registers page and resource verification redirects for each domain', function (string $environment): void {
        $registry = app(PanelRegistry::class);
        $panels = $registry->panels;
        $originalEnvironment = app()->environment();
        $panel = (clone Filament::getPanel('admin'))
            ->domains(['example.com', 'example2.com'])
            ->emailVerification()
            ->path('registration-domains');
        $registry->panels = [$panel->getId() => $panel];
        Filament::setCurrentPanel($panel);
        app()->instance('env', $environment);

        try {
            require __DIR__ . '/../../../../packages/panels/routes/web.php';

            Route::getRoutes()->refreshNameLookups();

            Filament::auth()->user()->forceFill(['email_verified_at' => null])->save();

            foreach (['example.com', 'example2.com'] as $domain) {
                $routeNamePrefix = "filament.admin.{$domain}.";

                foreach (['pages.dashboard', 'resources.posts.index'] as $routeName) {
                    $route = Route::getRoutes()->getByName($routeNamePrefix . $routeName);

                    expect($route->gatherMiddleware())
                        ->toContain("verified:{$routeNamePrefix}auth.email-verification.prompt");

                    $this->get(route($routeNamePrefix . $routeName))
                        ->assertRedirect("http://{$domain}/registration-domains/email-verification/prompt");
                }
            }
        } finally {
            $registry->panels = $panels;
            app()->instance('env', $originalEnvironment);
        }
    })->with(['testing', 'production']);

    it('restores domain context after nested route registration throws', function (): void {
        $panel = Filament::getPanel('multi-domain');
        $otherPanel = Panel::make()->id('other')->domains(['other.example.com', 'other.example.org']);
        Filament::setCurrentDomain('explicit.example.com');

        $panel->registerRoutesForDomain('example2.com', function () use ($panel, $otherPanel): void {
            expect($panel->generateRouteName('test'))->toBe('filament.multi-domain.example2.com.test')
                ->and($otherPanel->generateRouteName('test'))->toBe('filament.other.explicit.example.com.test');

            expect(fn () => $panel->registerRoutesForDomain('example.com', function () use ($panel): void {
                expect($panel->generateRouteName('test'))->toBe('filament.multi-domain.example.com.test');

                throw new RuntimeException('Route registration failed');
            }))->toThrow(RuntimeException::class, 'Route registration failed');

            expect($panel->generateRouteName('test'))->toBe('filament.multi-domain.example2.com.test');
        });

        expect($panel->generateRouteName('test'))->toBe('filament.multi-domain.explicit.example.com.test')
            ->and(Filament::getCurrentDomain())->toBe('explicit.example.com');

        Filament::setCurrentDomain(null);
        $environment = app()->environment();
        app()->instance('env', 'production');

        try {
            expect(fn () => $panel->generateRouteName('test'))->toThrow(LogicException::class);
        } finally {
            app()->instance('env', $environment);
        }
    });
});

describe('root page routes', function (): void {
    it('does not register the home route when a page already owns the root path and `domain()` contains a scheme', function (): void {
        expect(Route::getRoutes()->getByName('filament.single-domain.home'))->toBeNull()
            ->and(Route::getRoutes()->getByName('filament.single-domain.pages.dashboard'))->not->toBeNull();
    });

    it('preserves the dashboard route when registered on a panel without a domain', function (): void {
        expect(Route::getRoutes()->getByName('filament.admin.pages.dashboard'))->not->toBeNull();
    });
});
