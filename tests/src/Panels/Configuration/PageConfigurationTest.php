<?php

use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Pages\ConfigurableSettings;
use Filament\Tests\Fixtures\Pages\ConfigurableSettingsConfiguration;
use Filament\Tests\Panels\Configuration\TestCase;
use Livewire\Livewire;

uses(TestCase::class);

it('can register page configurations', function (): void {
    $configurations = Filament::getCurrentOrDefaultPanel()->getPageConfigurations();

    expect($configurations)->toHaveCount(2);

    $keys = collect($configurations)->map(fn ($configuration) => $configuration->getKey())->all();

    expect($keys)->toContain('general');
    expect($keys)->toContain('advanced');
});

it('can register default page without configuration', function (): void {
    $pages = Filament::getPages();

    expect($pages)->toContain(ConfigurableSettings::class);
});

it('can generate different slugs for each configuration', function (): void {
    Filament::forPageConfiguration(ConfigurableSettings::class, 'general');
    expect(ConfigurableSettings::getSlug())->toBe('general-settings');
    Filament::setCurrentPageConfigurationKey(null);

    Filament::forPageConfiguration(ConfigurableSettings::class, 'advanced');
    expect(ConfigurableSettings::getSlug())->toBe('advanced-settings');
    Filament::setCurrentPageConfigurationKey(null);
});

it('can access configuration using `getConfiguration()`', function (): void {
    // Without configuration context, returns null
    expect(ConfigurableSettings::getConfiguration())->toBeNull();

    // Set configuration context
    $panel = Filament::getCurrentOrDefaultPanel();
    $generalConfig = $panel->getPageConfiguration(ConfigurableSettings::class, 'general');

    Filament::setCurrentPageConfigurationKey('general');

    expect(ConfigurableSettings::getConfiguration())->toBe($generalConfig);
    expect(ConfigurableSettings::hasConfiguration())->toBeTrue();

    // Clean up
    Filament::setCurrentPageConfigurationKey(null);
});

it('can use `withConfiguration()` to execute callback in configuration context', function (): void {
    $result = ConfigurableSettings::withConfiguration('general', function () {
        $configuration = ConfigurableSettings::getConfiguration();

        return $configuration?->getKey();
    });

    expect($result)->toBe('general');

    // After callback, configuration context is restored
    expect(ConfigurableSettings::getConfiguration())->toBeNull();
});

it('can generate URLs for specific configurations', function (): void {
    $defaultUrl = ConfigurableSettings::getUrl();
    $generalUrl = ConfigurableSettings::getUrl(configuration: 'general');
    $advancedUrl = ConfigurableSettings::getUrl(configuration: 'advanced');

    expect($defaultUrl)->toContain('/configurable-settings');
    expect($generalUrl)->toContain('/general-settings');
    expect($advancedUrl)->toContain('/advanced-settings');
});

it('can render page with configuration context', function (): void {
    Filament::forPageConfiguration(ConfigurableSettings::class, 'general');

    Livewire::test(ConfigurableSettings::class)
        ->assertSuccessful()
        ->assertSet('settingsCategory', 'general');

    Filament::setCurrentPageConfigurationKey(null);

    Filament::forPageConfiguration(ConfigurableSettings::class, 'advanced');

    Livewire::test(ConfigurableSettings::class)
        ->assertSuccessful()
        ->assertSet('settingsCategory', 'advanced');

    Filament::setCurrentPageConfigurationKey(null);
});

it('can get navigation label from configuration', function (): void {
    // Default label
    expect(ConfigurableSettings::getNavigationLabel())->toBe('Configurable Settings');

    // General configuration label
    Filament::forPageConfiguration(ConfigurableSettings::class, 'general');
    expect(ConfigurableSettings::getNavigationLabel())->toBe('General Settings');
    Filament::setCurrentPageConfigurationKey(null);

    // Advanced configuration label
    Filament::forPageConfiguration(ConfigurableSettings::class, 'advanced');
    expect(ConfigurableSettings::getNavigationLabel())->toBe('Advanced Settings');
    Filament::setCurrentPageConfigurationKey(null);
});

it('can get navigation group from configuration', function (): void {
    // Default group (null)
    expect(ConfigurableSettings::getNavigationGroup())->toBeNull();

    // General configuration group
    Filament::forPageConfiguration(ConfigurableSettings::class, 'general');
    expect(ConfigurableSettings::getNavigationGroup())->toBe('Settings');
    Filament::setCurrentPageConfigurationKey(null);

    // Advanced configuration group
    Filament::forPageConfiguration(ConfigurableSettings::class, 'advanced');
    expect(ConfigurableSettings::getNavigationGroup())->toBe('Settings');
    Filament::setCurrentPageConfigurationKey(null);
});

it('can get navigation sort from configuration', function (): void {
    // Default sort
    expect(ConfigurableSettings::getNavigationSort())->toBe(2);

    // General configuration sort
    Filament::forPageConfiguration(ConfigurableSettings::class, 'general');
    expect(ConfigurableSettings::getNavigationSort())->toBe(1);
    Filament::setCurrentPageConfigurationKey(null);

    // Advanced configuration sort
    Filament::forPageConfiguration(ConfigurableSettings::class, 'advanced');
    expect(ConfigurableSettings::getNavigationSort())->toBe(2);
    Filament::setCurrentPageConfigurationKey(null);
});

it('throws exception when using `withConfiguration()` with unknown key', function (): void {
    ConfigurableSettings::withConfiguration('unknown', fn () => null);
})->throws(Exception::class, "Configuration 'unknown' not found for page");

it('can access configuration properties', function (): void {
    $panel = Filament::getCurrentOrDefaultPanel();
    $configurations = $panel->getPageConfigurations();

    $generalConfig = collect($configurations)->first(fn ($configuration) => $configuration->getKey() === 'general');

    /** @var ConfigurableSettingsConfiguration $generalConfig */
    expect($generalConfig->getSettingsCategory())->toBe('general');

    $advancedConfig = collect($configurations)->first(fn ($configuration) => $configuration->getKey() === 'advanced');

    /** @var ConfigurableSettingsConfiguration $advancedConfig */
    expect($advancedConfig->getSettingsCategory())->toBe('advanced');
});

it('can use `make()` factory method to create configuration', function (): void {
    $configuration = ConfigurableSettings::make('test')
        ->slug('test-settings')
        ->navigationLabel('Test Settings')
        ->settingsCategory('test');

    expect($configuration)->toBeInstanceOf(ConfigurableSettingsConfiguration::class);
    expect($configuration->getKey())->toBe('test');
    expect($configuration->getSlug())->toBe('test-settings');
    expect($configuration->getNavigationLabel())->toBe('Test Settings');
    expect($configuration->getSettingsCategory())->toBe('test');
});

it('preserves configuration context when calling `getUrl()` for a different configuration', function (): void {
    // Set up general configuration context
    Filament::forPageConfiguration(ConfigurableSettings::class, 'general');

    // Verify we're in general context
    expect(ConfigurableSettings::getConfiguration()?->getKey())->toBe('general');

    // Get URL for advanced configuration (should temporarily switch context)
    $advancedUrl = ConfigurableSettings::getUrl(configuration: 'advanced');

    // Verify we're still in general context after the call
    expect(ConfigurableSettings::getConfiguration()?->getKey())->toBe('general');
    expect($advancedUrl)->toContain('/advanced-settings');

    // Clean up
    Filament::setCurrentPageConfigurationKey(null);
});

it('preserves null configuration context when calling `getUrl()` with configuration', function (): void {
    // No configuration context set
    expect(ConfigurableSettings::getConfiguration())->toBeNull();

    // Get URL for general configuration
    $generalUrl = ConfigurableSettings::getUrl(configuration: 'general');

    // Verify context is still null after the call
    expect(ConfigurableSettings::getConfiguration())->toBeNull();
    expect($generalUrl)->toContain('/general-settings');
});
