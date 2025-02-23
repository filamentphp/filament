<?php

namespace Filament\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemaServiceProvider;
use Filament\SpatieLaravelSettingsPluginServiceProvider;
use Filament\SpatieLaravelTranslatablePluginServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Policies\DepartmentPolicy;
use Filament\Tests\Fixtures\Policies\TicketPolicy;
use Filament\Tests\Fixtures\Providers\AdminPanelProvider;
use Filament\Tests\Fixtures\Providers\CustomPanelProvider;
use Filament\Tests\Fixtures\Providers\DomainTenancyPanelProvider;
use Filament\Tests\Fixtures\Providers\EmailCodeAuthenticationPanelProvider;
use Filament\Tests\Fixtures\Providers\Fixtures\Providers\SingleDomainPanel;
use Filament\Tests\Fixtures\Providers\GoogleTwoFactorAuthenticationPanelProvider;
use Filament\Tests\Fixtures\Providers\MultiDomainPanel;
use Filament\Tests\Fixtures\Providers\RequiredMultiFactorAuthenticationPanelProvider;
use Filament\Tests\Fixtures\Providers\SlugsPanelProvider;
use Filament\Tests\Fixtures\Providers\TenancyPanelProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Kirschbaum\PowerJoins\PowerJoinsServiceProvider;
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
        $providers = [
            ActionsServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SchemaServiceProvider::class,
            SpatieLaravelSettingsPluginServiceProvider::class,
            SpatieLaravelTranslatablePluginServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            AdminPanelProvider::class,
            CustomPanelProvider::class,
            EmailCodeAuthenticationPanelProvider::class,
            GoogleTwoFactorAuthenticationPanelProvider::class,
            RequiredMultiFactorAuthenticationPanelProvider::class,
            DomainTenancyPanelProvider::class,
            MultiDomainPanel::class,
            SingleDomainPanel::class,
            SlugsPanelProvider::class,
            TenancyPanelProvider::class,
            PowerJoinsServiceProvider::class,
        ];

        sort($providers);

        return $providers;
    }

    protected function defineEnvironment($app): void
    {
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);

        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('view.paths', [
            ...$app['config']->get('view.paths'),
            __DIR__ . '/../resources/views',
        ]);
    }
}
