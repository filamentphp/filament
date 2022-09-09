@php
    $unreadNotificationsCount = $this->getUnreadDatabaseNotificationsCount();
@endphp

<div
    @if ($pollingInterval = $this->getPollingInterval())
        wire:poll.{{ $pollingInterval }}
    @endif
>
    @if ($databaseNotificationsButton = $this->getDatabaseNotificationsButton())
        <x-notifications::database.trigger>
            {{ $databaseNotificationsButton->with(['unreadNotificationsCount' => $unreadNotificationsCount]) }}
        </x-notifications::database.trigger>
    @endif

    <x-notifications::database.modal
        :notifications="$this->getDatabaseNotifications()"
        :unread-notifications-count="$unreadNotificationsCount"
    />
</div>
