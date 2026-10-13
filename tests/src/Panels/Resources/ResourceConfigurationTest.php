<?php

use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Resources\Posts\ConfigurablePostResource;
use Filament\Tests\Fixtures\Resources\Posts\Pages\ListConfigurablePosts;
use Filament\Tests\Fixtures\Resources\Posts\PostResourceConfiguration;
use Filament\Tests\Panels\Configuration\TestCase;
use Livewire\Livewire;

uses(TestCase::class);

describe('registration', function (): void {
    it('can register resource configurations', function (): void {
        $configurations = Filament::getCurrentOrDefaultPanel()->getResourceConfigurations();

        expect($configurations)->toHaveCount(2);

        $keys = collect($configurations)->map(fn ($configuration) => $configuration->getKey())->all();

        expect($keys)->toContain('featured');
        expect($keys)->toContain('archived');
    });

    it('can register default resource without configuration', function (): void {
        $resources = Filament::getResources();

        expect($resources)->toContain(ConfigurablePostResource::class);
    });

    it('can generate different slugs for each configuration', function (): void {
        Filament::forResourceConfiguration(ConfigurablePostResource::class, 'featured');
        expect(ConfigurablePostResource::getSlug())->toBe('featured-posts');
        Filament::setCurrentResourceConfigurationKey(null);

        Filament::forResourceConfiguration(ConfigurablePostResource::class, 'archived');
        expect(ConfigurablePostResource::getSlug())->toBe('archived-posts');
        Filament::setCurrentResourceConfigurationKey(null);
    });
});

describe('configuration access', function (): void {
    it('can access configuration using `getConfiguration()`', function (): void {
        expect(ConfigurablePostResource::getConfiguration())->toBeNull();

        $panel = Filament::getCurrentOrDefaultPanel();
        $featuredConfiguration = $panel->getResourceConfiguration(ConfigurablePostResource::class, 'featured');

        Filament::setCurrentResourceConfigurationKey('featured');

        expect(ConfigurablePostResource::getConfiguration())->toBe($featuredConfiguration);
        expect(ConfigurablePostResource::hasConfiguration())->toBeTrue();

        Filament::setCurrentResourceConfigurationKey(null);
    });

    it('can use `withConfiguration()` to execute callback in configuration context', function (): void {
        $result = ConfigurablePostResource::withConfiguration('featured', function () {
            $configuration = ConfigurablePostResource::getConfiguration();

            return $configuration?->getKey();
        });

        expect($result)->toBe('featured');

        expect(ConfigurablePostResource::getConfiguration())->toBeNull();
    });
});

describe('URLs and rendering', function (): void {
    it('can generate URLs for specific configurations', function (): void {
        $defaultUrl = ConfigurablePostResource::getUrl();
        $featuredUrl = ConfigurablePostResource::getUrl(configuration: 'featured');
        $archivedUrl = ConfigurablePostResource::getUrl(configuration: 'archived');

        expect($defaultUrl)->toContain('/configurable-posts');
        expect($featuredUrl)->toContain('/featured-posts');
        expect($archivedUrl)->toContain('/archived-posts');
    });

    it('can filter records based on configuration', function (): void {
        $author = User::factory()->create();

        $publishedPosts = Post::factory()
            ->count(3)
            ->for($author, 'author')
            ->create(['is_published' => true]);

        $unpublishedPosts = Post::factory()
            ->count(2)
            ->for($author, 'author')
            ->create(['is_published' => false]);

        Filament::forResourceConfiguration(ConfigurablePostResource::class, 'featured');

        Livewire::test(ListConfigurablePosts::class)
            ->assertCanSeeTableRecords($publishedPosts)
            ->assertCanNotSeeTableRecords($unpublishedPosts);

        Filament::setCurrentResourceConfigurationKey(null);

        Filament::forResourceConfiguration(ConfigurablePostResource::class, 'archived');

        Livewire::test(ListConfigurablePosts::class)
            ->assertCanSeeTableRecords($unpublishedPosts)
            ->assertCanNotSeeTableRecords($publishedPosts);

        Filament::setCurrentResourceConfigurationKey(null);
    });
});

describe('navigation properties', function (): void {
    it('can get navigation label from configuration', function (): void {
        expect(ConfigurablePostResource::getNavigationLabel())->toBe('Posts');

        Filament::forResourceConfiguration(ConfigurablePostResource::class, 'featured');
        expect(ConfigurablePostResource::getNavigationLabel())->toBe('Featured Posts');
        Filament::setCurrentResourceConfigurationKey(null);

        Filament::forResourceConfiguration(ConfigurablePostResource::class, 'archived');
        expect(ConfigurablePostResource::getNavigationLabel())->toBe('Archived Posts');
        Filament::setCurrentResourceConfigurationKey(null);
    });

    it('can get navigation group from configuration', function (): void {
        expect(ConfigurablePostResource::getNavigationGroup())->toBe('Blog');

        Filament::forResourceConfiguration(ConfigurablePostResource::class, 'featured');
        expect(ConfigurablePostResource::getNavigationGroup())->toBe('Featured Content');
        Filament::setCurrentResourceConfigurationKey(null);

        Filament::forResourceConfiguration(ConfigurablePostResource::class, 'archived');
        expect(ConfigurablePostResource::getNavigationGroup())->toBe('Archive');
        Filament::setCurrentResourceConfigurationKey(null);
    });

    it('can get navigation sort from configuration', function (): void {
        expect(ConfigurablePostResource::getNavigationSort())->toBeNull();

        Filament::forResourceConfiguration(ConfigurablePostResource::class, 'featured');
        expect(ConfigurablePostResource::getNavigationSort())->toBe(1);
        Filament::setCurrentResourceConfigurationKey(null);

        Filament::forResourceConfiguration(ConfigurablePostResource::class, 'archived');
        expect(ConfigurablePostResource::getNavigationSort())->toBe(100);
        Filament::setCurrentResourceConfigurationKey(null);
    });
});

describe('configuration factories and properties', function (): void {
    it('throws an `Exception` when using `withConfiguration()` with an unknown key', function (): void {
        ConfigurablePostResource::withConfiguration('unknown', fn () => null);
    })->throws(Exception::class, "Configuration 'unknown' not found for resource");

    it('can access configuration properties', function (): void {
        $panel = Filament::getCurrentOrDefaultPanel();
        $configurations = $panel->getResourceConfigurations();

        $featuredConfiguration = collect($configurations)->first(fn ($configuration) => $configuration->getKey() === 'featured');

        /** @var PostResourceConfiguration $featuredConfiguration */
        expect($featuredConfiguration->isFeatured())->toBeTrue();
        expect($featuredConfiguration->isArchived())->toBeFalse();

        $archivedConfiguration = collect($configurations)->first(fn ($configuration) => $configuration->getKey() === 'archived');

        /** @var PostResourceConfiguration $archivedConfiguration */
        expect($archivedConfiguration->isFeatured())->toBeFalse();
        expect($archivedConfiguration->isArchived())->toBeTrue();
    });

    it('can use `make()` factory method to create configuration', function (): void {
        $configuration = ConfigurablePostResource::make('test')
            ->slug('test-posts')
            ->navigationLabel('Test Posts')
            ->featured();

        expect($configuration)->toBeInstanceOf(PostResourceConfiguration::class);
        expect($configuration->getKey())->toBe('test');
        expect($configuration->getSlug())->toBe('test-posts');
        expect($configuration->getNavigationLabel())->toBe('Test Posts');
        expect($configuration->isFeatured())->toBeTrue();

        $configurationWithoutSlug = ConfigurablePostResource::make('custom');

        expect($configurationWithoutSlug->getSlug())->toBeNull();
    });
});

describe('URL configuration context', function (): void {
    it('preserves configuration context when calling `getUrl()` for a different configuration', function (): void {
        Filament::forResourceConfiguration(ConfigurablePostResource::class, 'featured');

        expect(ConfigurablePostResource::getConfiguration()?->getKey())->toBe('featured');

        $archivedUrl = ConfigurablePostResource::getUrl(configuration: 'archived');

        expect(ConfigurablePostResource::getConfiguration()?->getKey())->toBe('featured');
        expect($archivedUrl)->toContain('/archived-posts');

        Filament::setCurrentResourceConfigurationKey(null);
    });

    it('preserves `null` configuration context when calling `getUrl()` with configuration', function (): void {
        expect(ConfigurablePostResource::getConfiguration())->toBeNull();

        $featuredUrl = ConfigurablePostResource::getUrl(configuration: 'featured');

        expect(ConfigurablePostResource::getConfiguration())->toBeNull();
        expect($featuredUrl)->toContain('/featured-posts');
    });
});
