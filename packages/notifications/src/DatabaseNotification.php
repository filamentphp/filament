<?php

namespace Filament\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification as BaseNotification;

class DatabaseNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public array $data,
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return $this->data;
    }
}
