<?php

namespace Filament\Panel\Concerns;
use Filament\Livewire\DatabaseNotifications;

trait HasNotifications
{
    protected bool $hasDatabaseNotifications = false;

    protected ?string $databaseNotificationsPolling = '30s';

    protected string $databaseNotificationLivewire = DatabaseNotifications::class;

    public function databaseNotifications(bool $condition = true, string $livewire = DatabaseNotifications::class): static
    {
        $this->hasDatabaseNotifications = $condition;

        $this->databaseNotificationLivewire = $livewire;

        return $this;
    }

    public function databaseNotificationsPolling(?string $interval): static
    {
        $this->databaseNotificationsPolling = $interval;

        return $this;
    }

    public function hasDatabaseNotifications(): bool
    {
        return $this->hasDatabaseNotifications;
    }

    public function getDatabaseNotificationsPollingInterval(): ?string
    {
        return $this->databaseNotificationsPolling;
    }

    /**
     * Get the value of databaseNotificationLivewire
     */
    public function getDatabaseNotificationLivewire(): string
    {
        return $this->databaseNotificationLivewire;
    }
}
