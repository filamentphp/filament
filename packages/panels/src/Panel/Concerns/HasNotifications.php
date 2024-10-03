<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasNotifications
{
    protected bool | Closure $hasDatabaseNotifications = false;

    protected string | Closure | null $databaseNotificationsPolling = '30s';

    public function databaseNotifications(bool | Closure $condition = true): static
    {
        $this->hasDatabaseNotifications = $condition;

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
}
