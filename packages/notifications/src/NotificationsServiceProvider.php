<?php

namespace Filament\Notifications;

use Filament\Notifications\Http\Livewire\Notifications;
use Filament\Notifications\Testing\TestsNotifications;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\Response;
use Livewire\Testing\TestableLivewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NotificationsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('notifications')
            ->hasCommands(Commands\InstallCommand::class)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        Livewire::component('notifications', Notifications::class);

        Livewire::listen('component.dehydrate', function (Component $component, Response $response): Response {
            if (! Livewire::isLivewireRequest()) {
                return $response;
            }

            if ($component->redirectTo !== null) {
                return $response;
            }

            if (count(session()->get('filament.notifications') ?? []) > 0) {
                $component->emit('notificationsSent');
            }

            return $response;
        });

        TestableLivewire::mixin(new TestsNotifications());
    }
}
