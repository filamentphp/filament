<?php

namespace Filament\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\SpatieLaravelSettingsPluginServiceProvider;
use Filament\SpatieLaravelTranslatablePluginServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Tests\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            LivewireServiceProvider::class,
            SpatieLaravelSettingsPluginServiceProvider::class,
            SpatieLaravelTranslatablePluginServiceProvider::class,
            TablesServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('view.paths', array_merge(
            $app['config']->get('view.paths'),
            [__DIR__ . '/../resources/views'],
        ));
    }
}
