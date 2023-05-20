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

        <div class="mt-[calc(-1rem-1px)] -mx-6 border-b divide-y dark:border-gray-700">
            @foreach ($notifications as $notification)
                <div @class([
                    'bg-primary-50 dark:bg-gray-700' => $notification->unread(),
                ])>
                    {{ $this->getNotification($notification)->inline() }}
                </div>
            @endforeach
        </div>
    @else
        <x-filament-notifications::database.modal.empty-state />
    @endif
</x-filament::modal>
