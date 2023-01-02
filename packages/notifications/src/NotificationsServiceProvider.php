<?php

namespace Filament\Notifications;

use Filament\Notifications\Http\Livewire\Notifications;
use Filament\Notifications\Testing\TestsNotifications;
use Filament\Support\Assets\AssetManager;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
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
            ->name('filament-notifications')
            ->hasTranslations()
            ->hasViews();
    }

    public function packageRegistered(): void
    {
        $this->app->resolving(AssetManager::class, function () {
            FilamentAsset::register([
                Js::make('notifications', __DIR__ . '/../dist/index.js'),
            ], 'filament/notifications');
        });
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
