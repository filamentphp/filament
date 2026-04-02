<?php

use Filament\Tests\Panels\Pages\TestCase;
use Illuminate\Support\Facades\Route;

uses(TestCase::class);

it('registers the dashboard route when using a tenant domain binding field', function (): void {
    $route = Route::getRoutes()->getByName('filament.domain-tenancy.pages.dashboard');

    expect($route)->not->toBeNull()
        ->and($route->getDomain())->toBe('{tenant}')
        ->and($route->uri())->toBe('domain-tenancy');
});

it('does not register the home redirect when the dashboard occupies the root path with a tenant domain binding field', function (): void {
    expect(Route::getRoutes()->getByName('filament.domain-tenancy.home'))->toBeNull();
});
