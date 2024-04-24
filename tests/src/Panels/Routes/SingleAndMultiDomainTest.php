<?php

use Filament\Tests\Panels\Pages\TestCase;
use Illuminate\Support\Facades\Route;

uses(TestCase::class);

it('admin panel without any domain should resolve to default routes', function () {
    $routeName = 'filament.admin.auth.login';
    $route = Route::getRoutes()->getByName($routeName);
    expect($route)->not->toBeEmpty();
});

it('admin panel with single domain should resolve to default routes', function () {
    $routeName = 'filament.single-domain.auth.login';
    $route = Route::getRoutes()->getByName($routeName);
    expect($route)->not->toBeEmpty();

    $routeName = 'filament.single-domain.single.local.auth.login';
    $route = Route::getRoutes()->getByName($routeName);
    expect($route)->toBeEmpty();
});

it('admin panel with multi domain should resolve to their respective routes', function () {
    $routeName = 'filament.multi-domain.auth.login';
    $route = Route::getRoutes()->getByName($routeName);
    expect($route)->toBeEmpty(); // this route should never exists

    $routeName = 'filament.multi-domain.first.local.auth.login';
    $route = Route::getRoutes()->getByName($routeName);
    expect($route)->not->toBeEmpty(); // it should exists

    $routeName = 'filament.multi-domain.second.local.auth.login';
    $route = Route::getRoutes()->getByName($routeName);
    expect($route)->not->toBeEmpty(); // it should exists
});
