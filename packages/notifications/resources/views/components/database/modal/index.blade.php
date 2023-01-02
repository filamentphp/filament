@props([
    'notifications',
    'unreadNotificationsCount',
])

<x-filament::modal
    id="database-notifications"
    close-button
    slide-over
    width="md"
>
    @if ($notifications->count())
        <x-slot name="header">
            <x-filament-notifications::database.modal.heading
                :unread-notifications-count="$unreadNotificationsCount"
            />

            <x-filament-notifications::database.modal.actions
                :notifications="$notifications"
                :unread-notifications-count="$unreadNotificationsCount"
            />
        </x-slot>

        @foreach ($notifications as $notification)
            {{ $this->getNotificationFromDatabaseRecord($notification) }}
        @endforeach
    @else
        <x-filament-notifications::database.modal.empty-state />
    @endif
</x-filament::modal>
