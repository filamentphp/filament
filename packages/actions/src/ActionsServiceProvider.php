<?php

namespace Filament\Actions;

use Filament\Actions\Testing\TestsActions;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Routing\Router;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ActionsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-actions')
            ->hasCommands([
                Commands\MakeExporterCommand::class,
                Commands\MakeImporterCommand::class,
            ])
            ->hasMigrations([
                'create_imports_table',
                'create_exports_table',
                'create_failed_import_rows_table',
            ])
            ->hasRoutes('web')
            ->hasTranslations()
            ->hasViews();
    }

    public function packageRegistered(): void
    {
        app(Router::class)->middlewareGroup('filament.actions', ['web']);
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Js::make('actions', __DIR__ . '/../dist/index.js'),
        ], 'filament/actions');

        Testable::mixin(new TestsActions);
    }
}
