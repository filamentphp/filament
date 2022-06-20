<?php

namespace Filament;

use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\Response;

class NotificationManager
{
    protected array $notifications = [];

    public function notify(string $status, string $message): void
    {
        session()->push('filament.notifications', [
            'id' => uniqid(),
            'status' => $status,
            'message' => Str::markdown($message),
        ]);

        $this->notifications = session()->get('filament.notifications');
    }

    public function handleLivewireResponses(Component $component, Response $response): Response
    {
        if (! Livewire::isLivewireRequest()) {
            return $response;
        }

        if ($component->redirectTo !== null) {
            return $response;
        }

        $notifications = $this->notifications;
        session()->forget('filament.notifications');

        if (count($notifications) > 0) {
            $component->dispatchBrowserEvent('notify', $notifications);
        }

        return $response;
    }
}
