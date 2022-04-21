<?php

namespace Filament;

use Livewire\Component;
use Livewire\Response;

class NotificationManager
{
    protected static array $notifications = [];

    public static function notify(string $status, string $message): void
    {
        session()->push('notifications', [
            'id' => uniqid(),
            'status' => $status,
            'message' => $message,
        ]);

        static::$notifications = session()->get('notifications');
    }

    public static function handleLivewireResponses(Component $component, Response $response): Response
    {
        if ($component->redirectTo !== null) {
            return $response;
        }

        $notifications = static::$notifications;

        if (count($notifications) > 0) {
            $component->dispatchBrowserEvent('notify', $notifications);
        }

        return $response;
    }
}
