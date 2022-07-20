<?php

namespace Filament\Notifications;

use Livewire\Component;
use Livewire\Livewire;
use Livewire\Response;

class NotificationManager
{
    public function notify(Notification | string $notification, ?string $message): void
    {
        if (! $notification instanceof Notification) {
            $notification = Notification::make()
                ->title($message)
                ->status($notification);
        }

        session()->push(
            'filament.notifications',
            $notification->toLivewire(),
        );
    }

    public function handleLivewireResponse(Component $component, Response $response): Response
    {
        if (! Livewire::isLivewireRequest()) {
            return $response;
        }

        if ($component->redirectTo !== null) {
            return $response;
        }

        if (count(session()->get('filament.notifications', [])) > 0) {
            $component->emit('dispatchNewNotifications');
        }

        return $response;
    }
}
