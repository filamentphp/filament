<?php

use Filament\Tests\Panels\Pages\TestCase;
use Illuminate\Support\Facades\Route;

uses(TestCase::class);

it('panels without any domain should not use the domain in the names of routes', function () {
    $routeName = 'filament.admin.auth.login';
    $route = Route::getRoutes()->getByName($routeName);
    expect($route)->not->toBeEmpty();
});

it('panels with a single domain should not use the domain in the names of routes', function () {
    $routeName = 'filament.single-domain.auth.login';
    $route = Route::getRoutes()->getByName($routeName);
    expect($route)->not->toBeEmpty();

    $routeName = 'filament.single-domain.example3.com.auth.login';
    $route = Route::getRoutes()->getByName($routeName);
    expect($route)->toBeEmpty();
});

it('panels with multiple domains should use the domain in names of all routes', function () {
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
