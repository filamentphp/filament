<?php

use Filament\Facades\Filament;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Tests\Panels\Fixtures\Pages\Settings;
use Filament\Tests\Panels\Fixtures\Resources\PostCategoryResource;
use Filament\Tests\Panels\Fixtures\Resources\PostResource;
use Filament\Tests\Panels\Fixtures\Resources\ProductResource;
use Filament\Tests\Panels\Fixtures\Resources\UserResource;
use Filament\Tests\Panels\Navigation\TestCase;

uses(TestCase::class);

it('can register navigation', function () {
    Filament::getCurrentPanel()->navigation(function (NavigationBuilder $navigation): NavigationBuilder {
        return $navigation
            ->items([
                ...Dashboard::getNavigationItems(),
                ...UserResource::getNavigationItems(),
                ...Settings::getNavigationItems(),
            ])
            ->groups([
                NavigationGroup::make('Blog')
                    ->items([
                        ...PostResource::getNavigationItems(),
                        ...PostCategoryResource::getNavigationItems(),
                    ]),
                NavigationGroup::make('Shop')
                    ->items([
                        ...ProductResource::getNavigationItems(),
                    ]),
            ]);
    });

    expect(Filament::getNavigation())
        ->sequence(
            fn ($group) => $group
                ->toBeInstanceOf(NavigationGroup::class)
                ->getLabel()->toBeNull()
                ->getItems()
                ->sequence(
                    fn ($item) => $item
                        ->getLabel()->toBe('Dashboard')
                        ->getIcon()->toBe('heroicon-o-home'),
                    fn ($item) => $item
                        ->getLabel()->toBe('Users')
                        ->getIcon()->toBe('heroicon-o-user'),
                    fn ($item) => $item
                        ->getLabel()->toBe('Settings')
                        ->getIcon()->toBe('heroicon-o-cog-6-tooth'),
                )
                ->each->toBeInstanceOf(NavigationItem::class),
            fn ($group) => $group
                ->toBeInstanceOf(NavigationGroup::class)
                ->getLabel()->toBe('Blog')
                ->getItems()
                ->sequence(
                    fn ($item) => $item
                        ->getLabel()->toBe('Posts')
                        ->getIcon()->toBe('heroicon-o-document-text'),
                    fn ($item) => $item
                        ->getLabel()->toBe('Post Categories')
                        ->getIcon()->toBe('heroicon-o-rectangle-stack'),
                )
                ->each->toBeInstanceOf(NavigationItem::class),
            fn ($group) => $group
                ->toBeInstanceOf(NavigationGroup::class)
                ->getLabel()->toBe('Shop')
                ->getItems()
                ->sequence(
                    fn ($item) => $item
                        ->getLabel()->toBe('Products')
                        ->getIcon()->toBe('heroicon-o-shopping-bag'),
                )
                ->each->toBeInstanceOf(NavigationItem::class),
        );
});

it('can register navigation groups individually', function () {
    Filament::getCurrentPanel()->navigation(function (NavigationBuilder $navigation): NavigationBuilder {
        return $navigation
            ->group('Blog', [
                ...PostResource::getNavigationItems(),
                ...PostCategoryResource::getNavigationItems(),
            ]);
    });

    expect(Filament::getNavigation())
        ->sequence(
            fn ($group) => $group
                ->toBeInstanceOf(NavigationGroup::class)
                ->getLabel()->toBe('Blog')
                ->getItems()
                ->sequence(
                    fn ($item) => $item
                        ->getLabel()->toBe('Posts')
                        ->getIcon()->toBe('heroicon-o-document-text'),
                    fn ($item) => $item
                        ->getLabel()->toBe('Post Categories')
                        ->getIcon()->toBe('heroicon-o-rectangle-stack'),
                )
                ->each->toBeInstanceOf(NavigationItem::class),
        );
});
