<?php

namespace Filament\Actions;

use Filament\Actions\Testing\TestsActions;
use Filament\Notifications\Http\Livewire\Notifications;
use Filament\Notifications\Testing\TestsNotifications;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\Asset;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\Response;
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
