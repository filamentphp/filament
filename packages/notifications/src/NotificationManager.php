<?php

namespace Filament\Notifications;

use Livewire\Component;
use Livewire\Livewire;
use Livewire\Response;

class NotificationManager
{
    public function send(Notification $notification): void
    {
        session()->push(
            'filament.notifications',
            $notification->toArray(),
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
            $component->emit('notificationsSent');
        }

        return $response;
    }
}
