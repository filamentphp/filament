<?php

namespace Filament;

use Livewire\Response;

class NotificationManager
{
    protected static $notifications = [];

    public static function notify(string $status, string $message): void
    {
        session()->push('notifications', [
            'id' => uniqid(),
            'status' => $status,
            'message' => $message,
        ]);

        self::$notifications = session()->get('notifications');
    }

    public static function handleLivewireResponses($component, Response $response): Response
    {
        if ($component->redirectTo !== null) {
            return $response;
        }

        $notifications = self::$notifications;

        if (count($notifications) > 0) {
            $component->dispatchBrowserEvent('notify', $notifications);
        }

        return $response;
    }
}
