<?php

use Filament\Tests\Fixtures\Pages\ConfigurableSettings;
use Filament\Tests\Fixtures\Resources\Posts\ConfigurablePostResource;
use Filament\Tests\Panels\Configuration\TestCase;
use Illuminate\Support\Facades\Route;

uses(TestCase::class);

describe('resource routes', function (): void {
    it('registers default resource route', function (): void {
        $routeName = 'filament.configuration.resources.posts.configurable-posts.index';
        $route = Route::getRoutes()->getByName($routeName);

        expect($route)->not->toBeNull();
        expect($route->uri())->toContain('configurable-posts');
    });

    it('registers resource configuration routes with correct slugs', function (): void {
        // Featured posts route
        $featuredRouteName = 'filament.configuration.resources.featured-posts.index';
        $featuredRoute = Route::getRoutes()->getByName($featuredRouteName);

        expect($featuredRoute)->not->toBeNull();
        expect($featuredRoute->uri())->toContain('featured-posts');

        // Archived posts route
        $archivedRouteName = 'filament.configuration.resources.archived-posts.index';
        $archivedRoute = Route::getRoutes()->getByName($archivedRouteName);

        expect($archivedRoute)->not->toBeNull();
        expect($archivedRoute->uri())->toContain('archived-posts');
    });
});

describe('page routes', function (): void {
    it('registers default page route', function (): void {
        $routeName = 'filament.configuration.pages.configurable-settings';
        $route = Route::getRoutes()->getByName($routeName);

        expect($route)->not->toBeNull();
        expect($route->uri())->toContain('configurable-settings');
    });

    it('registers page configuration routes with correct slugs', function (): void {
        // General settings route
        $generalRouteName = 'filament.configuration.pages.general-settings';
        $generalRoute = Route::getRoutes()->getByName($generalRouteName);

        expect($generalRoute)->not->toBeNull();
        expect($generalRoute->uri())->toContain('general-settings');

        // Advanced settings route
        $advancedRouteName = 'filament.configuration.pages.advanced-settings';
        $advancedRoute = Route::getRoutes()->getByName($advancedRouteName);

        expect($advancedRoute)->not->toBeNull();
        expect($advancedRoute->uri())->toContain('advanced-settings');
    });

    it('applies resource configuration middleware to configuration routes', function (): void {
        // Featured posts route should have configuration middleware
        $featuredRouteName = 'filament.configuration.resources.featured-posts.index';
        $featuredRoute = Route::getRoutes()->getByName($featuredRouteName);

        expect($featuredRoute->middleware())->toContain('resource-configuration:featured');

        // Archived posts route should have configuration middleware
        $archivedRouteName = 'filament.configuration.resources.archived-posts.index';
        $archivedRoute = Route::getRoutes()->getByName($archivedRouteName);

        expect($archivedRoute->middleware())->toContain('resource-configuration:archived');
    });

    it('applies page configuration middleware to configuration routes', function (): void {
        // General settings route should have configuration middleware
        $generalRouteName = 'filament.configuration.pages.general-settings';
        $generalRoute = Route::getRoutes()->getByName($generalRouteName);

        expect($generalRoute->middleware())->toContain('page-configuration:general');

        // Advanced settings route should have configuration middleware
        $advancedRouteName = 'filament.configuration.pages.advanced-settings';
        $advancedRoute = Route::getRoutes()->getByName($advancedRouteName);

        expect($advancedRoute->middleware())->toContain('page-configuration:advanced');
    });
});

it('does not apply configuration middleware to default resource route', function (): void {
    $routeName = 'filament.configuration.resources.posts.configurable-posts.index';
    $route = Route::getRoutes()->getByName($routeName);

    $configMiddleware = collect($route->middleware())
        ->filter(fn (string $middleware) => str_starts_with($middleware, 'resource-configuration:'))
        ->values()
        ->all();

    expect($configMiddleware)->toBeEmpty();
});

it('does not apply configuration middleware to default page route', function (): void {
    $routeName = 'filament.configuration.pages.configurable-settings';
    $route = Route::getRoutes()->getByName($routeName);

    $configMiddleware = collect($route->middleware())
        ->filter(fn (string $middleware) => str_starts_with($middleware, 'page-configuration:'))
        ->values()
        ->all();

    expect($configMiddleware)->toBeEmpty();
});

it('generates correct URLs for resource configurations', function (): void {
    $defaultUrl = ConfigurablePostResource::getUrl();
    $featuredUrl = ConfigurablePostResource::getUrl(configuration: 'featured');
    $archivedUrl = ConfigurablePostResource::getUrl(configuration: 'archived');

    // URLs should be different
    expect($defaultUrl)->not->toBe($featuredUrl);
    expect($defaultUrl)->not->toBe($archivedUrl);
    expect($featuredUrl)->not->toBe($archivedUrl);

    // URLs should contain correct slugs
    expect($defaultUrl)->toContain('/configurable-posts');
    expect($featuredUrl)->toContain('/featured-posts');
    expect($archivedUrl)->toContain('/archived-posts');
});

it('generates correct URLs for page configurations', function (): void {
    $defaultUrl = ConfigurableSettings::getUrl();
    $generalUrl = ConfigurableSettings::getUrl(configuration: 'general');
    $advancedUrl = ConfigurableSettings::getUrl(configuration: 'advanced');

    // URLs should be different
    expect($defaultUrl)->not->toBe($generalUrl);
    expect($defaultUrl)->not->toBe($advancedUrl);
    expect($generalUrl)->not->toBe($advancedUrl);

    // URLs should contain correct slugs
    expect($defaultUrl)->toContain('/configurable-settings');
    expect($generalUrl)->toContain('/general-settings');
    expect($advancedUrl)->toContain('/advanced-settings');
});
