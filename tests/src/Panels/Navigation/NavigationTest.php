<?php

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Panels\Navigation\TestCase;

uses(TestCase::class);

it('can register navigation items from resources and pages', function (): void {
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
                        ->getLabel()->toBe('Actions'),
                    fn ($item) => $item
                        ->getLabel()->toBe('Departments')
                        ->getIcon()->toBe(Heroicon::OutlinedRectangleStack),
                    fn ($item) => $item
                        ->getLabel()->toBe('Tickets')
                        ->getIcon()->toBe(Heroicon::OutlinedRectangleStack),
                    fn ($item) => $item
                        ->getLabel()->toBe('Ticket Messages')
                        ->getIcon()->toBe(Heroicon::OutlinedRectangleStack),
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

it('can reorder navigation groups by registering them', function (): void {
    Filament::getCurrentOrDefaultPanel()->navigationGroups([
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

it('can reorder navigation groups by registering them with different labels', function (): void {
    Filament::getCurrentOrDefaultPanel()->navigationGroups([
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

it('can reorder navigation groups by registering their labels', function (): void {
    Filament::getCurrentOrDefaultPanel()->navigationGroups([
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
