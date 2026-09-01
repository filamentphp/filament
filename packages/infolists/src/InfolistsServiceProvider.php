<?php

namespace Filament\Infolists;

use Filament\Infolists\Testing\TestsInfolistActions;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class InfolistsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-infolists')
            ->hasCommands([
                Commands\MakeEntryCommand::class,
            ])
            ->hasTranslations()
            ->hasViews();
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

        Testable::mixin(new TestsInfolistActions);
    }
}
