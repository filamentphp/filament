<?php

namespace Filament\Notifications;

use Filament\Notifications\Facades\Notification;
use Filament\Notifications\Http\Livewire\Notifications;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NotificationsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('notifications')
            ->hasCommands($this->getCommands())
            ->hasConfigFile()
            ->hasViews();
    }

    protected function getCommands(): array
    {
        return [
            Commands\InstallCommand::class,
        ];
    }

    public function packageBooted(): void
    {
        Livewire::listen('component.dehydrate', [Notification::class, 'handleLivewireResponse']);
        Livewire::component('notifications', Notifications::class);
    }
}
