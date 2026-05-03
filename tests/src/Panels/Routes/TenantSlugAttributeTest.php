<?php

use Filament\Tests\Panels\Pages\TestCase;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

uses(TestCase::class);

it('registers the dashboard route when using a tenant slug attribute', function (): void {
    $route = Route::getRoutes()->getByName('filament.slug-tenancy.pages.dashboard');

    expect($route)->not->toBeNull()
        ->and($route->uri())->toBe('slug-tenancy/{tenant}');
});

it('does not register the home redirect when the dashboard occupies the root path with a tenant slug attribute', function (): void {
    expect(Route::getRoutes()->getByName('filament.slug-tenancy.home'))->toBeNull();
});

it('allows application routes to take precedence over tenant root routes when using a tenant slug attribute', function (): void {
    Route::get('/slug-tenancy/up', fn (): string => 'ok')->name('status');

    $route = Route::getRoutes()->match(Request::create('/slug-tenancy/up'));

    expect($route->getName())->toBe('status');
});
