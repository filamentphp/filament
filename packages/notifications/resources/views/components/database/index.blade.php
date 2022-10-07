@php
    $notifications = $this->getDatabaseNotifications();
    $unreadNotificationsCount = $this->getUnreadDatabaseNotificationsCount();
@endphp

<div
    @if ($pollingInterval = $this->getPollingInterval())
        wire:poll.{{ $pollingInterval }}
    @endif
    class="flex items-center"
>
    @if ($databaseNotificationsTrigger = $this->getDatabaseNotificationsTrigger())
        <x-notifications::database.trigger>
            {{ $databaseNotificationsTrigger->with(['unreadNotificationsCount' => $unreadNotificationsCount]) }}
        </x-notifications::database.trigger>
    @endif

    <x-notifications::database.modal
        :notifications="$notifications"
        :unread-notifications-count="$unreadNotificationsCount"
    />
</div>
