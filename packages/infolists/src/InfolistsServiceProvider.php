<?php

namespace Filament\Infolists;

use Filament\Infolists\Testing\TestsInfolistActions;
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
        Testable::mixin(new TestsInfolistActions);
    }
}
