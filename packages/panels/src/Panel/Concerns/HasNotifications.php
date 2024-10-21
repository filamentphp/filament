<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Actions\Action;

trait HasNotifications
{
    protected bool | Closure $hasDatabaseNotifications = false;

    protected string | Closure | null $databaseNotificationsPolling = '30s';

    protected ?Closure $modifyDatabaseNotificationsMarkAllAsReadUsing = null;

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

    public function databaseNotificationsMarkAllAsReadAction(?Closure $callback): static
    {
        $this->modifyDatabaseNotificationsMarkAllAsReadUsing = $callback;

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

    public function getDatabaseNotificationsMarkAllAsReadAction(): Action
    {
        $action = Action::make('markAllNotificationsAsRead')
            ->link()
            ->label(__('filament-notifications::database.modal.actions.mark_all_as_read.label'))
            ->extraAttributes(['tabindex' => '-1'])
            ->action('markAllNotificationsAsRead');

        if ($this->modifyDatabaseNotificationsMarkAllAsReadUsing) {
            $action = $this->evaluate($this->modifyDatabaseNotificationsMarkAllAsReadUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }
}
