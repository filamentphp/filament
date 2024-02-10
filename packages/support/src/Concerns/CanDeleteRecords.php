<?php

namespace Filament\Support\Concerns;

use Closure;
use Filament\Notifications\Notification;

trait CanDeleteRecords
{
    protected bool | Closure $deletable = true;

    protected string | Closure | null $notDeletableNotificationTitle = null;

    protected string | Closure  | null $notDeletableNotificationBody = null;

    public function deletable(bool | Closure $condition = true): static
    {
        $this->deletable = $condition;

        return $this;
    }

    public function isDeletable(): bool
    {
        return (bool) $this->evaluate($this->deletable);
    }

    public function notDeletableNotificationTitle(string | Closure $notDeletableNotificationTitle): static
    {
        $this->notDeletableNotificationTitle = $notDeletableNotificationTitle;

        return $this;
    }

    public function getNotDeletableNotificationTitle(): string
    {
        return $this->evaluate($this->notDeletableNotificationTitle);
    }

    public function notDeletableNotificationBody(string | Closure $notDeletableNotificationBody): static
    {
        $this->notDeletableNotificationBody = $notDeletableNotificationBody;

        return $this;
    }

    public function getNotDeletableNotificationBody(): string
    {
        return $this->evaluate($this->notDeletableNotificationBody);
    }

    public function sendNotDeletableNotification(): void
    {
        Notification::make()
            ->title($this->getNotDeletableNotificationTitle())
            ->body($this->getNotDeletableNotificationBody())
            ->danger()
            ->send();
    }
}
