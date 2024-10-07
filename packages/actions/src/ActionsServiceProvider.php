<?php

namespace Filament\Actions;

use Filament\Actions\Testing\TestsActions;
use Illuminate\Filesystem\Filesystem;
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
            ->hasRoute('web')
            ->hasTranslations()
            ->hasViews();
    }

    public function packageRegistered(): void
    {
        app(Router::class)->middlewareGroup('filament.actions', ['web', 'auth']);
    }

    public function packageBooted(): void
    {
        if ($this->app->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament/{$file->getFilename()}"),
                ], 'filament-stubs');
            }
        }

        Testable::mixin(new TestsActions);
    }
}
