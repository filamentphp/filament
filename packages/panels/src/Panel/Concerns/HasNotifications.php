<?php

namespace Filament\Panel\Concerns;

trait HasNotifications
{
    protected bool $hasDatabaseNotifications = false;

    protected ?string $databaseNotificationsPolling = '30s';

    public function databaseNotifications(bool $condition = true): static
    {
        $this->hasDatabaseNotifications = $condition;

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
}
