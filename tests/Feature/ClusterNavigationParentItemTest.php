<?php

namespace Tests\Feature;

use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationBuilder;
use Filament\Panel;
use Filament\Panel\Concerns\HasNavigation;

it('child item appears under parent inside cluster', function () {

    // Mock panel
    $panel = new class extends Panel {
        use HasNavigation;

        public function __construct()
        {
            $this->navigation(function () {
                return (new NavigationBuilder)
                    ->groups($this->getNavigationGroups());
            });
        }
    };

    // Create parent navigation item
    $parentItem = NavigationItem::make('Users');

    // Create child navigation item
    $childItem = NavigationItem::make('Products');

    // Manually attach child to parent
    $parentItem->childItems([$childItem]);

    // Create a navigation group and add the parent item only
    $group = NavigationGroup::make('System')
        ->items([$parentItem]);

    // Assign the group to the panel
    $panel->navigationGroups([$group]);

    // Build navigation
    $navigation = $panel->buildNavigation();

    // Find the group
    $systemGroup = collect($navigation)
        ->first(fn ($g) => $g->getLabel() === 'System');

    expect($systemGroup)->not()->toBeNull();

    // Find the parent
    $parent = collect($systemGroup->getItems())
        ->first(fn ($i) => $i->getLabel() === 'Users');

    expect($parent)->not()->toBeNull();

    // Find the child under parent
    $child = collect($parent->getChildItems())
        ->first(fn ($i) => $i->getLabel() === 'Products');

    expect($child)->not()->toBeNull();
});


it('parent item without child appears correctly', function () {
    $panel = new class extends Panel {
        use HasNavigation;

        public function __construct()
        {
            $this->navigation(fn() => (new NavigationBuilder)->groups($this->getNavigationGroups()));
        }
    };

    // Parent without any child
    $parentItem = NavigationItem::make('Settings');

    // Another item with child for control
    $childItem = NavigationItem::make('Products')
        ->parentItem('Users');

    $parentWithChild = NavigationItem::make('Users');

    $group = NavigationGroup::make('System')
        ->items([$parentItem, $parentWithChild, $childItem]);

    $panel->navigationGroups([$group]);

    $navigation = $panel->buildNavigation();

    $systemGroup = collect($navigation)
        ->first(fn ($g) => $g->getLabel() === 'System');

    expect($systemGroup)->not()->toBeNull();

    // Check parent without child is still present
    $settingsParent = collect($systemGroup->getItems())
        ->first(fn ($i) => $i->getLabel() === 'Settings');

    expect($settingsParent)->not()->toBeNull();
    expect($settingsParent->getChildItems())->toBeEmpty();

    // Check parent with child has child attached
    $usersParent = collect($systemGroup->getItems())
        ->first(fn ($i) => $i->getLabel() === 'Users');

    $child = collect($usersParent->getChildItems())
        ->first(fn ($i) => $i->getLabel() === 'Products');

    expect($child)->not()->toBeNull();
});
