<?php

namespace Filament;

use Filament\Pages\Page;
use Livewire\Component;
use Livewire\Response;

class NotificationManager
{
    protected array $notifications = [];

    public function notify(string $status, string $message): void
    {
        session()->push('notifications', [
            'id' => uniqid(),
            'status' => $status,
            'message' => $message,
        ]);

        $this->notifications = session()->get('notifications');
    }

    public function handleLivewireResponses(Component $component, Response $response): Response
    {
        if (! $component instanceof Page) {
            return $response;
        }

        if ($component->redirectTo !== null) {
            return $response;
        }

        $notifications = $this->notifications;
        session()->forget('notifications');

        if (count($notifications) > 0) {
            $component->dispatchBrowserEvent('notify', $notifications);
        }

        return $response;
    }
}
