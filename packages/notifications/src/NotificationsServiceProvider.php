<?php

namespace Filament\Notifications;

use Filament\Notifications\Http\Livewire\Manager;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NotificationsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('notifications')
            ->hasViews();
    }

    public function packageBooted(): void
    {
        Livewire::component('filament.notifications', Manager::class);
    }
}
