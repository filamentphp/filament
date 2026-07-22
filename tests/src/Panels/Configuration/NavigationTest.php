<?php

use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Tests\Fixtures\Pages\ConfigurableSettings;
use Filament\Tests\Fixtures\Resources\Posts\ConfigurablePostResource;
use Filament\Tests\Panels\Configuration\TestCase;

uses(TestCase::class);

describe('resource configuration navigation', function (): void {
    it('registers separate navigation items for each resource configuration', function (): void {
        $navigation = Filament::getNavigation();

        // Flatten navigation items from all groups
        $items = collect($navigation)
            ->flatMap(fn ($group) => $group->getItems())
            ->filter(fn (NavigationItem $item) => str_contains($item->getUrl(), 'posts'))
            ->values();

        // Should have 3 items: default, featured, and archived
        expect($items)->toHaveCount(3);

        $urls = $items->map(fn (NavigationItem $item) => $item->getUrl())->all();

        expect($urls)->toContain(ConfigurablePostResource::getUrl());
        expect($urls)->toContain(ConfigurablePostResource::getUrl(configuration: 'featured'));
        expect($urls)->toContain(ConfigurablePostResource::getUrl(configuration: 'archived'));
    });

    it('registers separate navigation items for each resource configuration when one is active', function (): void {
        $defaultUrl = ConfigurablePostResource::getUrl();

        Filament::forResourceConfiguration(ConfigurablePostResource::class, 'featured');

        $items = collect(Filament::getNavigation())
            ->flatMap(fn ($group) => $group->getItems())
            ->filter(fn (NavigationItem $item) => str_contains($item->getUrl(), 'posts'))
            ->values();

        expect($items)->toHaveCount(3);

        $urls = $items->map(fn (NavigationItem $item) => $item->getUrl())->all();

        expect($urls)->toContain($defaultUrl);
        expect($urls)->toContain(ConfigurablePostResource::getUrl(configuration: 'featured'));
        expect($urls)->toContain(ConfigurablePostResource::getUrl(configuration: 'archived'));
    });

    it('preserves the active resource configuration key when mounting navigation', function (): void {
        Filament::forResourceConfiguration(ConfigurablePostResource::class, 'featured');

        Filament::getNavigation();

        expect(Filament::getCurrentResourceConfigurationKey())->toBe('featured');
    });

    it('shows correct navigation labels for resource configurations', function (): void {
        $navigation = Filament::getNavigation();

        // Flatten navigation items from all groups
        $items = collect($navigation)
            ->flatMap(fn ($group) => $group->getItems())
            ->filter(fn (NavigationItem $item) => str_contains($item->getUrl(), 'posts'))
            ->values();

        $labels = $items->map(fn (NavigationItem $item) => $item->getLabel())->all();

        expect($labels)->toContain('Posts'); // Default
        expect($labels)->toContain('Featured Posts');
        expect($labels)->toContain('Archived Posts');
    });

    it('places resource configuration navigation items in correct groups', function (): void {
        $navigation = Filament::getNavigation();

        // Find groups that contain post-related items
        $groupsWithPosts = collect($navigation)
            ->filter(function ($group) {
                return collect($group->getItems())
                    ->filter(fn (NavigationItem $item) => str_contains($item->getUrl(), 'posts'))
                    ->isNotEmpty();
            })
            ->map(fn ($group) => $group->getLabel())
            ->values()
            ->all();

        expect($groupsWithPosts)->toContain('Blog'); // Default
        expect($groupsWithPosts)->toContain('Featured Content');
        expect($groupsWithPosts)->toContain('Archive');
    });
});

describe('page configuration navigation', function (): void {
    it('registers separate navigation items for each page configuration', function (): void {
        $navigation = Filament::getNavigation();

        // Flatten navigation items from all groups
        $items = collect($navigation)
            ->flatMap(fn ($group) => $group->getItems())
            ->filter(fn (NavigationItem $item) => str_contains($item->getUrl(), 'settings'))
            ->values();

        // Should have 3 items: default, general, and advanced
        expect($items)->toHaveCount(3);

        $urls = $items->map(fn (NavigationItem $item) => $item->getUrl())->all();

        expect($urls)->toContain(ConfigurableSettings::getUrl());
        expect($urls)->toContain(ConfigurableSettings::withConfiguration('general', fn () => ConfigurableSettings::getUrl()));
        expect($urls)->toContain(ConfigurableSettings::withConfiguration('advanced', fn () => ConfigurableSettings::getUrl()));
    });

    it('registers separate navigation items for each page configuration when one is active', function (): void {
        $defaultUrl = ConfigurableSettings::getUrl();

        Filament::forPageConfiguration(ConfigurableSettings::class, 'general');

        $items = collect(Filament::getNavigation())
            ->flatMap(fn ($group) => $group->getItems())
            ->filter(fn (NavigationItem $item) => str_contains($item->getUrl(), 'settings'))
            ->values();

        expect($items)->toHaveCount(3);

        $urls = $items->map(fn (NavigationItem $item) => $item->getUrl())->all();

        expect($urls)->toContain($defaultUrl);
        expect($urls)->toContain(ConfigurableSettings::withConfiguration('general', fn () => ConfigurableSettings::getUrl()));
        expect($urls)->toContain(ConfigurableSettings::withConfiguration('advanced', fn () => ConfigurableSettings::getUrl()));
    });

    it('preserves the active page configuration key when mounting navigation', function (): void {
        Filament::forPageConfiguration(ConfigurableSettings::class, 'general');

        Filament::getNavigation();

        expect(Filament::getCurrentPageConfigurationKey())->toBe('general');
    });

    it('shows correct navigation labels for page configurations', function (): void {
        $navigation = Filament::getNavigation();

        // Flatten navigation items from all groups
        $items = collect($navigation)
            ->flatMap(fn ($group) => $group->getItems())
            ->filter(fn (NavigationItem $item) => str_contains($item->getUrl(), 'settings'))
            ->values();

        $labels = $items->map(fn (NavigationItem $item) => $item->getLabel())->all();

        expect($labels)->toContain('Configurable Settings'); // Default
        expect($labels)->toContain('General Settings');
        expect($labels)->toContain('Advanced Settings');
    });

    it('places page configuration navigation items in correct groups', function (): void {
        $navigation = Filament::getNavigation();

        // Find items in Settings group
        $settingsGroup = collect($navigation)
            ->first(fn ($group) => $group->getLabel() === 'Settings');

        expect($settingsGroup)->not->toBeNull();

        $items = collect($settingsGroup->getItems())
            ->filter(fn (NavigationItem $item) => str_contains($item->getUrl(), 'settings'))
            ->values();

        // Both general and advanced should be in Settings group
        expect($items)->toHaveCount(2);

        $labels = $items->map(fn (NavigationItem $item) => $item->getLabel())->all();

        expect($labels)->toContain('General Settings');
        expect($labels)->toContain('Advanced Settings');
    });
});
