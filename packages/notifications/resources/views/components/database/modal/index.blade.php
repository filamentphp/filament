@props([
    'notifications',
    'unreadNotificationsCount',
])

<x-notifications::modal
    id="database-notifications"
    close-button
    slide-over
    width="md"
>
    @if ($notifications->count())
        <x-slot name="header">
            <x-notifications::database.modal.heading
                :unread-notifications-count="$unreadNotificationsCount"
            />

            <x-notifications::database.modal.actions
                :notifications="$notifications"
                :unread-notifications-count="$unreadNotificationsCount"
            />
        </x-slot>

        <div class="-mt-4">
            @foreach ($notifications as $notification)
                <div class="-mx-4 border-b">
                    <div @class([
                        'p-4 -mx-2',
                        'bg-gray-50' => $notification->unread(),
                    ])>
                        {{ \Filament\Notifications\Notification::fromDatabase($notification)->inline() }}
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <x-notifications::database.modal.empty-state />
    @endif
</x-notifications::modal>
