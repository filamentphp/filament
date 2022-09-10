@php
    $unreadNotificationsCount = $this->getUnreadDatabaseNotificationsCount();
@endphp

<div class="flex items-center"
    @if ($pollingInterval = $this->getPollingInterval())
        wire:poll.{{ $pollingInterval }}
    @endif
>
    @if ($databaseNotificationsTrigger = $this->getDatabaseNotificationsTrigger())
        <x-notifications::database.trigger>
            {{ $databaseNotificationsTrigger->with(['unreadNotificationsCount' => $unreadNotificationsCount]) }}
        </x-notifications::database.trigger>
    @endif

    <x-notifications::database.modal
        :notifications="$this->getDatabaseNotifications()"
        :unread-notifications-count="$unreadNotificationsCount"
    />
</div>
