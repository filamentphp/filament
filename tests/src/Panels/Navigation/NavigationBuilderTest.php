<?php

use Filament\Facades\Filament;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Pages\Settings;
use Filament\Tests\Fixtures\Resources\PostCategories\PostCategoryResource;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Fixtures\Resources\Shop\Products\ProductResource;
use Filament\Tests\Fixtures\Resources\Users\UserResource;
use Filament\Tests\Panels\Navigation\TestCase;

uses(TestCase::class);

it('can register navigation', function (): void {
    Filament::getCurrentOrDefaultPanel()->navigation(function (NavigationBuilder $navigation): NavigationBuilder {
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
                        ->getIcon()->toBe(Heroicon::OutlinedHome),
                    fn ($item) => $item
                        ->getLabel()->toBe('Users')
                        ->getIcon()->toBe(Heroicon::OutlinedUser),
                    fn ($item) => $item
                        ->getLabel()->toBe('Settings')
                        ->getIcon()->toBe(Heroicon::OutlinedCog6Tooth),
                )
                ->each->toBeInstanceOf(NavigationItem::class),
            fn ($group) => $group
                ->toBeInstanceOf(NavigationGroup::class)
                ->getLabel()->toBe('Blog')
                ->getItems()
                ->sequence(
                    fn ($item) => $item
                        ->getLabel()->toBe('Posts')
                        ->getIcon()->toBe(Heroicon::OutlinedDocumentText),
                    fn ($item) => $item
                        ->getLabel()->toBe('Post Categories')
                        ->getIcon()->toBe(Heroicon::OutlinedRectangleStack),
                )
                ->each->toBeInstanceOf(NavigationItem::class),
            fn ($group) => $group
                ->toBeInstanceOf(NavigationGroup::class)
                ->getLabel()->toBe('Shop')
                ->getItems()
                ->sequence(
                    fn ($item) => $item
                        ->getLabel()->toBe('Products')
                        ->getIcon()->toBe(Heroicon::OutlinedShoppingBag),
                )
                ->each->toBeInstanceOf(NavigationItem::class),
        );
});

it('can register navigation groups individually', function (): void {
    Filament::getCurrentOrDefaultPanel()->navigation(function (NavigationBuilder $navigation): NavigationBuilder {
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
                        ->getIcon()->toBe(Heroicon::OutlinedDocumentText),
                    fn ($item) => $item
                        ->getLabel()->toBe('Post Categories')
                        ->getIcon()->toBe(Heroicon::OutlinedRectangleStack),
                )
                ->each->toBeInstanceOf(NavigationItem::class),
        );
});
