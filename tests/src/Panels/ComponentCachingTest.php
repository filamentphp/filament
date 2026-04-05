<?php

use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\ConfigurableSettings;
use Filament\Tests\Fixtures\Pages\Settings;
use Filament\Tests\Fixtures\Resources\Posts\ConfigurablePostResource;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Fixtures\Resources\Users\UserResource;
use Filament\Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

afterEach(function (): void {
    Filament::getPanel('admin')->clearCachedComponents();
    Filament::getPanel('configuration')->clearCachedComponents();
});

describe('caching and restoring', function (): void {
    it('can cache and restore resources', function (): void {
        $panel = Filament::getPanel('admin');

        $resourcesBefore = $panel->getResources();

        $panel->cacheComponents();
        $panel->restoreCachedComponents();

        $resourcesAfter = $panel->getResources();

        expect($resourcesAfter)->toEqual($resourcesBefore);
        expect($resourcesAfter)->toContain(PostResource::class);
        expect($resourcesAfter)->toContain(UserResource::class);
    });

    it('can cache and restore pages', function (): void {
        $panel = Filament::getPanel('admin');

        $pagesBefore = $panel->getPages();

        $panel->cacheComponents();
        $panel->restoreCachedComponents();

        $pagesAfter = $panel->getPages();

        expect($pagesAfter)->toEqual($pagesBefore);
        expect($pagesAfter)->toContain(Settings::class);
    });

    it('can cache and restore widgets', function (): void {
        $panel = Filament::getPanel('admin');

        $widgetsBefore = $panel->getWidgets();

        $panel->cacheComponents();
        $panel->restoreCachedComponents();

        $widgetsAfter = $panel->getWidgets();

        expect($widgetsAfter)->toEqual($widgetsBefore);
    });

});

describe('deduplication and clearing', function (): void {
    it('skips duplicate resource class strings when cached', function (): void {
        $panel = Filament::getPanel('admin');

        $panel->cacheComponents();
        $panel->restoreCachedComponents();

        // Calling `resources()` again with cached components should not add duplicates
        $countBefore = count($panel->getResources());
        $panel->resources([PostResource::class]);
        $countAfter = count($panel->getResources());

        expect($countAfter)->toBe($countBefore);
    });

    it('skips duplicate page class strings when cached', function (): void {
        $panel = Filament::getPanel('admin');

        $panel->cacheComponents();
        $panel->restoreCachedComponents();

        // Calling `pages()` again with cached components should not add duplicates
        $countBefore = count($panel->getPages());
        $panel->pages([Settings::class]);
        $countAfter = count($panel->getPages());

        expect($countAfter)->toBe($countBefore);
    });

    it('can clear cached components', function (): void {
        $panel = Filament::getPanel('admin');

        $panel->cacheComponents();

        expect(file_exists($panel->getComponentCachePath()))->toBeTrue();

        $panel->clearCachedComponents();

        expect(file_exists($panel->getComponentCachePath()))->toBeFalse();
    });

});

describe('configuration preservation', function (): void {
    it('preserves resource configurations after caching and restoring components', function (): void {
        $panel = Filament::getPanel('configuration');

        $panel->cacheComponents();
        $panel->restoreCachedComponents();

        // Verify configurations still exist
        $configurations = $panel->getResourceConfigurations();
        expect($configurations)->toHaveCount(2);

        // Verify configuration properties are preserved (not just class and key)
        $featuredConfig = $panel->getResourceConfiguration(ConfigurablePostResource::class, 'featured');
        expect($featuredConfig)->not->toBeNull();
        expect($featuredConfig->getSlug())->toBe('featured-posts');
        expect($featuredConfig->getNavigationLabel())->toBe('Featured Posts');
        expect($featuredConfig->getNavigationGroup())->toBe('Featured Content');
        expect($featuredConfig->getNavigationSort())->toBe(1);
        expect($featuredConfig->isFeatured())->toBeTrue();
        expect($featuredConfig->isArchived())->toBeFalse();

        $archivedConfig = $panel->getResourceConfiguration(ConfigurablePostResource::class, 'archived');
        expect($archivedConfig)->not->toBeNull();
        expect($archivedConfig->getSlug())->toBe('archived-posts');
        expect($archivedConfig->getNavigationLabel())->toBe('Archived Posts');
        expect($archivedConfig->getNavigationGroup())->toBe('Archive');
        expect($archivedConfig->getNavigationSort())->toBe(100);
        expect($archivedConfig->isFeatured())->toBeFalse();
        expect($archivedConfig->isArchived())->toBeTrue();
    });

    it('preserves page configurations after caching and restoring components', function (): void {
        $panel = Filament::getPanel('configuration');

        $panel->cacheComponents();
        $panel->restoreCachedComponents();

        // Verify configurations still exist
        $configurations = $panel->getPageConfigurations();
        expect($configurations)->toHaveCount(2);

        // Verify configuration properties are preserved (not just class and key)
        $generalConfig = $panel->getPageConfiguration(ConfigurableSettings::class, 'general');
        expect($generalConfig)->not->toBeNull();
        expect($generalConfig->getSlug())->toBe('general-settings');
        expect($generalConfig->getNavigationLabel())->toBe('General Settings');
        expect($generalConfig->getNavigationGroup())->toBe('Settings');
        expect($generalConfig->getNavigationSort())->toBe(1);
        expect($generalConfig->getSettingsCategory())->toBe('general');

        $advancedConfig = $panel->getPageConfiguration(ConfigurableSettings::class, 'advanced');
        expect($advancedConfig)->not->toBeNull();
        expect($advancedConfig->getSlug())->toBe('advanced-settings');
        expect($advancedConfig->getNavigationLabel())->toBe('Advanced Settings');
        expect($advancedConfig->getNavigationGroup())->toBe('Settings');
        expect($advancedConfig->getNavigationSort())->toBe(2);
        expect($advancedConfig->getSettingsCategory())->toBe('advanced');
    });
});
