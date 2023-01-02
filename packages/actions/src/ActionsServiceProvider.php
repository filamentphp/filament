<?php

namespace Filament\Actions;

use Filament\Actions\Testing\TestsActions;
use Livewire\Testing\TestableLivewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ActionsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-actions')
            ->hasTranslations()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        TestableLivewire::mixin(new TestsActions());
    }
}
