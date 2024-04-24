<?php

namespace Filament\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\SpatieLaravelSettingsPluginServiceProvider;
use Filament\SpatieLaravelTranslatablePluginServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Tests\Models\User;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;
    use WithWorkbench;

    protected function getPackageProviders($app): array
    {
        return [
            ActionsServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SpatieLaravelSettingsPluginServiceProvider::class,
            SpatieLaravelTranslatablePluginServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            AdminPanelProvider::class,
            CustomPanelProvider::class,
            SlugsPanelProvider::class,
            SingleDomainPanel::class,
            MultiDomainPanel::class,
            TenancyPanelProvider::class,
            DomainTenancyPanelProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('view.paths', [
            ...$app['config']->get('view.paths'),
            __DIR__ . '/../resources/views',
        ]);
    }
}
