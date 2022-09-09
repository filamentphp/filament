<?php

namespace Filament\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class BroadcastNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public array $data,
    ) {
    }

    public function via($notifiable): array
    {
        return ['broadcast'];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->data);
    }
}
