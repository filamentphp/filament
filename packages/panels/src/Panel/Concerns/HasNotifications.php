<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasNotifications
{
    protected bool | Closure $hasDatabaseNotifications = false;

    protected string | Closure | null $databaseNotificationsPolling = '30s';

    protected null | bool | Closure $broadcastEnabled = null;

    public function databaseNotifications(bool | Closure $condition = true, bool | Closure $broadcastEnabled = false): static
    {
        $this->hasDatabaseNotifications = $condition;
        $this->broadcastEnabled = $broadcastEnabled;

        return $this;
    }

    public function databaseNotificationsPolling(string | Closure | null $interval): static
    {
        $this->databaseNotificationsPolling = $interval;

        return $this;
    }

    public function hasDatabaseNotifications(): bool
    {
        return (bool) $this->evaluate($this->hasDatabaseNotifications);
    }

    public function getDatabaseNotificationsPollingInterval(): ?string
    {
        return $this->evaluate($this->databaseNotificationsPolling);
    }

    public function broadcastEnabled(): ?bool
    {
        return $this->evaluate($this->broadcastEnabled);
    }
}
