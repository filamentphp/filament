<?php

namespace Filament\Concerns;

use Filament\Pages\Page;

trait CanNotify
{
    protected function notify(string $status, string $message): void
    {
        $notification = [
            'message' => $message,
            'status' => $status,
        ];

        if ($this instanceof Page) {
            $this->notification = $notification;
        } else {
            $this->emitUp('setNotification', $notification);
        }
    }
}
