<?php

namespace Filament\Actions;

use Filament\Actions\Testing\TestsActions;
use Livewire\Features\SupportTesting\Testable;
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
        Testable::mixin(new TestsActions());
    }
}
