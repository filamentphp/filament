<?php

namespace Filament\Notifications;

use Filament\Notifications\Livewire\DatabaseNotifications;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Testing\TestsNotifications;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

use function Livewire\on;
use function Livewire\store;

class NotificationsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-notifications')
            ->hasTranslations()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Js::make('notifications', __DIR__ . '/../dist/index.js'),
        ], 'filament/notifications');

        Livewire::component('database-notifications', DatabaseNotifications::class);

        Livewire::component('notifications', Notifications::class);

        on('dehydrate', function (Component $component): void {
            if (! Livewire::isLivewireRequest()) {
                return;
            }

            if (store($component)->has('redirect')) {
                return;
            }

            if (count(session()->get('filament.notifications') ?? []) <= 0) {
                return;
            }

            $component->dispatch('notificationsSent');
        });

        Testable::mixin(new TestsNotifications);
    }
}
