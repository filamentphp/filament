<?php

use Filament\Actions\ActionsServiceProvider;
use Filament\Billing\Providers\StripeBillingProviderServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\SpatieLaravelTranslatablePluginServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Tests\AdminFilamentProvider;
use Filament\Tests\App\Navigation\NavigationServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Concerns\CreatesApplication;
use Orchestra\Testbench\Foundation\Application;

$basePathLocator = new class()
{
    use CreatesApplication;
};

$app = (new Application($basePathLocator::applicationBasePath()))
    ->configure([
        'enables_package_discoveries' => true,
    ])
    ->createApplication();

$app->register(AdminFilamentProvider::class);
$app->register(LivewireServiceProvider::class);
$app->register(FilamentServiceProvider::class);
$app->register(ActionsServiceProvider::class);
$app->register(FormsServiceProvider::class);
$app->register(NavigationServiceProvider::class);
$app->register(NotificationsServiceProvider::class);
$app->register(SpatieLaravelTranslatablePluginServiceProvider::class);
$app->register(SupportServiceProvider::class);
$app->register(TablesServiceProvider::class);
$app->register(WidgetsServiceProvider::class);

return $app;
