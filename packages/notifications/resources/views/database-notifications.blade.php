@php
    $notifications = $this->getNotifications();
    $unreadNotificationsCount = $this->getUnreadNotificationsCount();
    $markAllNotificationsAsReadAction = $this->getMarkAllNotificationsAsReadAction();
@endphp

<div
    @if ($pollingInterval = $this->getPollingInterval())
        wire:poll.{{ $pollingInterval }}
    @endif
    class="flex"
>
    @if ($trigger = $this->getTrigger())
        <x-filament-notifications::database.trigger>
            {{ $trigger->with(['unreadNotificationsCount' => $unreadNotificationsCount]) }}
        </x-filament-notifications::database.trigger>
    @endif

    <x-filament-notifications::database.modal
        :notifications="$notifications"
        :unread-notifications-count="$unreadNotificationsCount"
        :mark-all-notifications-as-read-action="$markAllNotificationsAsReadAction"
    />

    @if ($broadcastChannel = $this->getBroadcastChannel())
        <x-filament-notifications::database.echo
            :channel="$broadcastChannel"
        />
    @endif
</div>
