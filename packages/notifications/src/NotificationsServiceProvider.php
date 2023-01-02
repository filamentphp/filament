<?php

namespace Filament\Notifications;

use Filament\Notifications\Http\Livewire\Notifications;
use Filament\Notifications\Testing\TestsNotifications;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Js;
use Filament\Support\PluginServiceProvider;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\Response;
use Livewire\Testing\TestableLivewire;

class NotificationsServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-notifications';

    public function packageBooted(): void
    {
        parent::packageBooted();

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

    protected function getAssetPackage(): ?string
    {
        return 'filament/notifications';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            Js::make('notifications', __DIR__ . '/../dist/index.js'),
        ];
    }
}
