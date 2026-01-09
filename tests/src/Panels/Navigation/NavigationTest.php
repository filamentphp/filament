<?php

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Tests\Fixtures\Clusters\UserManagement\Pages\ManageAdmins;
use Filament\Tests\Panels\Navigation\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can register navigation items from resources and pages', function (): void {
    $navigation = Filament::getNavigation();
    $groups = collect($navigation);

    expect($groups)->each->toBeInstanceOf(NavigationGroup::class);

    $allItems = $groups->flatMap(fn (NavigationGroup $group) => $group->getItems());
    expect($allItems)->each->toBeInstanceOf(NavigationItem::class);

    $itemLabels = $allItems->map(fn (NavigationItem $item) => $item->getLabel());

    // Verify core pages and resources are registered
    expect($itemLabels)
        ->toContain('Dashboard')
        ->toContain('Companies')
        ->toContain('Users')
        ->toContain('Posts')
        ->toContain('Products');

    // Verify navigation groups contain expected items
    $groupLabels = $groups->map(fn (NavigationGroup $group) => $group->getLabel());
    expect($groupLabels)->toContain(null)->toContain('Blog')->toContain('Shop');

    $blogItemLabels = collect($groups->first(fn (NavigationGroup $group) => $group->getLabel() === 'Blog')->getItems())
        ->map(fn (NavigationItem $item) => $item->getLabel());
    expect($blogItemLabels)->toContain('Posts')->toContain('Post Categories');

    $shopItemLabels = collect($groups->first(fn (NavigationGroup $group) => $group->getLabel() === 'Shop')->getItems())
        ->map(fn (NavigationItem $item) => $item->getLabel());
    expect($shopItemLabels)->toContain('Products');

    // Verify Dashboard appears first (has lowest sort order)
    $defaultGroupItems = collect($groups->first(fn (NavigationGroup $group) => $group->getLabel() === null)->getItems());
    expect($defaultGroupItems->first()->getLabel())->toBe('Dashboard');
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

it('can establish parent-child relationships in sub-navigation', function (): void {
    // Create a test class that uses the `HasSubNavigation` trait
    $page = new class extends Page
    {
        protected string $view = 'filament-panels::pages.page';

        public function getSubNavigation(): array
        {
            return [
                NavigationItem::make('Users')
                    ->url('/users'),
                NavigationItem::make('Products')
                    ->parentItem('Users')
                    ->url('/products'),
            ];
        }
    };

    $navigation = $page->getCachedSubNavigation();

    // Should have one group
    expect($navigation)->toHaveCount(1);

    $group = $navigation[0];
    $items = $group->getItems();

    // Should only have the parent item at the top level (child is nested)
    expect($items)->toHaveCount(1);
    expect($items[0]->getLabel())->toBe('Users');

    // Parent should have the child item
    $childItems = $items[0]->getChildItems();
    expect($childItems)->toHaveCount(1);
    expect($childItems->first()->getLabel())->toBe('Products');
});

it('keeps parent items without children in sub-navigation', function (): void {
    $page = new class extends Page
    {
        protected string $view = 'filament-panels::pages.page';

        public function getSubNavigation(): array
        {
            return [
                NavigationItem::make('Settings')
                    ->url('/settings'),
                NavigationItem::make('Users')
                    ->url('/users'),
                NavigationItem::make('Products')
                    ->parentItem('Users')
                    ->url('/products'),
            ];
        }
    };

    $navigation = $page->getCachedSubNavigation();

    $group = $navigation[0];
    $items = collect($group->getItems());

    // Should have two top-level items: Settings and Users
    expect($items)->toHaveCount(2);

    $settings = $items->first(fn ($i) => $i->getLabel() === 'Settings');
    $users = $items->first(fn ($i) => $i->getLabel() === 'Users');

    expect($settings)->not()->toBeNull();
    expect($settings->getChildItems())->toBeEmpty();

    expect($users)->not()->toBeNull();
    expect($users->getChildItems())->toHaveCount(1);
    expect($users->getChildItems()->first()->getLabel())->toBe('Products');
});

it('handles child items with non-existent parent in sub-navigation', function (): void {
    $page = new class extends Page
    {
        protected string $view = 'filament-panels::pages.page';

        public function getSubNavigation(): array
        {
            return [
                NavigationItem::make('Settings')
                    ->url('/settings'),
                NavigationItem::make('Products')
                    ->parentItem('NonExistentParent')
                    ->url('/products'),
            ];
        }
    };

    $navigation = $page->getCachedSubNavigation();

    $group = $navigation[0];
    $items = collect($group->getItems());

    // Only Settings should appear (Products has non-existent parent and is dropped)
    expect($items)->toHaveCount(1);
    expect($items->first()->getLabel())->toBe('Settings');
});

it('establishes parent-child relationships within navigation groups', function (): void {
    $page = new class extends Page
    {
        protected string $view = 'filament-panels::pages.page';

        public function getSubNavigation(): array
        {
            return [
                NavigationGroup::make('System'),
                NavigationItem::make('Users')
                    ->group('System')
                    ->url('/users'),
                NavigationItem::make('Roles')
                    ->group('System')
                    ->parentItem('Users')
                    ->url('/roles'),
            ];
        }
    };

    $navigation = $page->getCachedSubNavigation();

    // Find the System group
    $systemGroup = collect($navigation)->first(fn ($g) => $g->getLabel() === 'System');
    expect($systemGroup)->not()->toBeNull();

    $items = collect($systemGroup->getItems());

    // Should only have Users at top level
    expect($items)->toHaveCount(1);
    expect($items->first()->getLabel())->toBe('Users');

    // Users should have Roles as child
    $childItems = $items->first()->getChildItems();
    expect($childItems)->toHaveCount(1);
    expect($childItems->first()->getLabel())->toBe('Roles');
});

it('supports multiple children under one parent in sub-navigation', function (): void {
    $page = new class extends Page
    {
        protected string $view = 'filament-panels::pages.page';

        public function getSubNavigation(): array
        {
            return [
                NavigationItem::make('Users')
                    ->url('/users'),
                NavigationItem::make('Roles')
                    ->parentItem('Users')
                    ->url('/roles'),
                NavigationItem::make('Permissions')
                    ->parentItem('Users')
                    ->url('/permissions'),
            ];
        }
    };

    $navigation = $page->getCachedSubNavigation();

    $group = $navigation[0];
    $items = $group->getItems();

    // Should only have Users at top level
    expect($items)->toHaveCount(1);
    expect($items[0]->getLabel())->toBe('Users');

    // Users should have both Roles and Permissions as children
    $childItems = $items[0]->getChildItems();
    expect($childItems)->toHaveCount(2);

    $childLabels = $childItems->map(fn ($item) => $item->getLabel())->all();
    expect($childLabels)->toContain('Roles');
    expect($childLabels)->toContain('Permissions');
});

it('can use enum `HasLabel` for cluster sub-navigation groups', function (): void {
    // Access a page within the cluster
    $this->get(ManageAdmins::getUrl())->assertSuccessful();

    // Get the page instance to test sub-navigation (pages within cluster also have sub-navigation)
    $component = livewire(ManageAdmins::class);

    $subNavigation = $component->instance()->getCachedSubNavigation();

    // Should have groups with proper labels from `HasLabel` interface
    $groupLabels = collect($subNavigation)
        ->filter(fn (NavigationGroup $group) => filled($group->getLabel()))
        ->map(fn (NavigationGroup $group) => $group->getLabel())
        ->values()
        ->all();

    // The enum `NavigationGroupEnum` has `getLabel()` returning 'User Management' for Users
    // and 'System Settings' for Settings - NOT the raw enum name like 'Users' or 'Settings'
    expect($groupLabels)->toContain('User Management');
    expect($groupLabels)->toContain('System Settings');
    expect($groupLabels)->not->toContain('Users');
    expect($groupLabels)->not->toContain('Settings');
});
