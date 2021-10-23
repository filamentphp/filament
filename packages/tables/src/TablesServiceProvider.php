<?php

namespace Filament\Tables;

use Filament\Tables\Commands\InstallCommand;
use Laravel\Ui\UiCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TablesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('tables')
            ->hasCommand(InstallCommand::class)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        UiCommand::macro('tables', function (UiCommand $command) {
            TablesPreset::install();

            $command->info('Scaffolding installed successfully.');

            $command->comment('Please run "npm install && npm run dev" to compile your new assets.');
        });
    }
}
