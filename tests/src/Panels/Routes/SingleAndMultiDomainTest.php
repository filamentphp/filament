<?php

use Filament\Tests\Panels\Pages\TestCase;
use Illuminate\Support\Facades\Route;

uses(TestCase::class);

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

it('does not register the home route when a page already owns the root path', function (): void {
    expect(Route::getRoutes()->getByName('filament.single-domain.home'))->toBeNull()
        ->and(Route::getRoutes()->getByName('filament.single-domain.pages.dashboard'))->not->toBeNull();
});

it('preserves the dashboard route when registered on a panel without a domain', function (): void {
    expect(Route::getRoutes()->getByName('filament.admin.pages.dashboard'))->not->toBeNull();
});
