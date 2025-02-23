<?php

use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\DomainTeam;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Panels\Pages\TestCase;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

uses(TestCase::class);

it('resolves the tenant correctly from the route', function (): void {
    $team = Team::factory()->create();

    $panel = Filament::getPanel('tenancy');
    Filament::setCurrentPanel($panel);
    Filament::setTenant($team);

    $routeName = 'filament.tenancy.resources.posts.index';
    $route = Route::getRoutes()->getByName($routeName);

    $request = Request::create(route($routeName, [
        'tenant' => $team,
    ]));

    $request->setRouteResolver(fn () => $route->bind($request));

    $resolvedTenant = $panel->getTenant($team->getKey());
    expect($resolvedTenant)->toBeSameModel($team);
});

it('resolves the tenant correctly using domain', function (): void {
    $team = DomainTeam::factory()->create();
    $panel = Filament::getPanel('domain-tenancy');

    Filament::setCurrentPanel($panel);
    Filament::setTenant($team);

    $routeName = 'filament.domain-tenancy.resources.posts.index';
    $route = Route::getRoutes()->getByName($routeName);

    $request = Request::create(route($routeName, [
        'tenant' => $team,
    ]));

    expect($request->fullUrl())->toStartWith('http://' . $team->domain);

    $request->setRouteResolver(fn () => $route->bind($request));

    $resolvedTenant = $panel->getTenant($team->getRouteKey());
    expect($resolvedTenant)->toBeSameModel($team);
});
