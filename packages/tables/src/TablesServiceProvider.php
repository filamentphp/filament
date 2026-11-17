<?php

namespace Filament\Tables;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tables\Testing\TestsActions;
use Filament\Tables\Testing\TestsBulkActions;
use Filament\Tables\Testing\TestsColumns;
use Filament\Tables\Testing\TestsFilters;
use Filament\Tables\Testing\TestsRecords;
use Filament\Tables\Testing\TestsSummaries;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TablesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-tables')
            ->hasCommands([
                Commands\MakeColumnCommand::class,
                Commands\MakeLivewireTableCommand::class,
                Commands\MakeTableCommand::class,
            ])
            ->hasTranslations()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            AlpineComponent::make('columns/checkbox', __DIR__ . '/../dist/components/columns/checkbox.js'),
            AlpineComponent::make('columns/select', __DIR__ . '/../dist/components/columns/select.js'),
            AlpineComponent::make('columns/text-input', __DIR__ . '/../dist/components/columns/text-input.js'),
            AlpineComponent::make('columns/toggle', __DIR__ . '/../dist/components/columns/toggle.js'),
            Js::make('tables', __DIR__ . '/../dist/index.js'),
        ], 'filament/tables');

        if ($this->app->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament/{$file->getFilename()}"),
                ], 'filament-stubs');
            }
        }

        Testable::mixin(new TestsActions);
        Testable::mixin(new TestsBulkActions);
        Testable::mixin(new TestsColumns);
        Testable::mixin(new TestsFilters);
        Testable::mixin(new TestsRecords);
        Testable::mixin(new TestsSummaries);
    }
}
