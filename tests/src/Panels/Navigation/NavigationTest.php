<?php

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Tests\Panels\Navigation\TestCase;

uses(TestCase::class);

it('can register navigation items from resources and pages', function () {
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
                        ->getLabel()->toBe('Actions'),
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

it('can reorder navigation groups by registering them', function () {
    Filament::getCurrentPanel()->navigationGroups([
        NavigationGroup::make()->label('Shop'),
        NavigationGroup::make()->label('Blog'),
    ]);

    expect(Filament::getNavigation())
        ->sequence(
            fn ($group) => $group
                ->toBeInstanceOf(NavigationGroup::class)
                ->getLabel()->toBeNull(),
            fn ($group) => $group
                ->toBeInstanceOf(NavigationGroup::class)
                ->getLabel()->toBe('Shop'),
            fn ($group) => $group
                ->toBeInstanceOf(NavigationGroup::class)
                ->getLabel()->toBe('Blog'),
        );
});

it('can reorder navigation groups by registering them with different labels', function () {
    Filament::getCurrentPanel()->navigationGroups([
        'Shop' => NavigationGroup::make()->label('Store'),
        'Blog' => NavigationGroup::make()->label('Posts'),
    ]);

    expect(Filament::getNavigation())
        ->sequence(
            fn ($group) => $group
                ->toBeInstanceOf(NavigationGroup::class)
                ->getLabel()->toBeNull(),
            fn ($group) => $group
                ->toBeInstanceOf(NavigationGroup::class)
                ->getLabel()->toBe('Store'),
            fn ($group) => $group
                ->toBeInstanceOf(NavigationGroup::class)
                ->getLabel()->toBe('Posts'),
        );
});

it('can reorder navigation groups by registering their labels', function () {
    Filament::getCurrentPanel()->navigationGroups([
        'Shop',
        'Blog',
    ]);

    expect(Filament::getNavigation())
        ->sequence(
            fn ($group) => $group
                ->toBeInstanceOf(NavigationGroup::class)
                ->getLabel()->toBeNull(),
            fn ($group) => $group
                ->toBeInstanceOf(NavigationGroup::class)
                ->getLabel()->toBe('Shop'),
            fn ($group) => $group
                ->toBeInstanceOf(NavigationGroup::class)
                ->getLabel()->toBe('Blog'),
        );
});
