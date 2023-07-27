@props([
    'notifications',
    'unreadNotificationsCount',
])

@php
    $hasNotifications = $notifications->count();
@endphp

<x-filament::modal
    :alignment="$hasNotifications ? null : 'center'"
    close-button
    :description="$hasNotifications ? null : __('filament-notifications::database.modal.empty.description')"
    :heading="$hasNotifications ? null : __('filament-notifications::database.modal.empty.heading')"
    :icon="$hasNotifications ? null : 'heroicon-o-bell-slash'"
    :icon-alias="$hasNotifications ? null : 'notifications::database.modal.empty-state'"
    :icon-color="$hasNotifications ? null : 'gray'"
    id="database-notifications"
    slide-over
    width="md"
>
    @if ($hasNotifications)
        <x-slot name="header">
            <div>
                <x-filament-notifications::database.modal.heading
                    :unread-notifications-count="$unreadNotificationsCount"
                />

                <x-filament-notifications::database.modal.actions
                    :notifications="$notifications"
                    :unread-notifications-count="$unreadNotificationsCount"
                />
            </div>
        </x-slot>

        <div class="-mx-6 divide-y divide-gray-100 dark:divide-white/10">
            @foreach ($notifications as $notification)
                <div
                    @class([
                        'relative before:absolute before:start-0 before:h-full before:w-0.5 before:bg-primary-600 dark:before:bg-primary-500' => $notification->unread(),
                    ])
                >
                    {{ $this->getNotification($notification)->inline() }}
                </div>
            @endforeach
        </div>

        @if ($notifications instanceof \Illuminate\Contracts\Pagination\Paginator && $notifications->hasPages())
            <x-slot name="footer">
                <nav
                    aria-label="{{ __('filament-notifications::database.modal.pagination.label') }}"
                    role="navigation"
                    class="flex items-center justify-between gap-3"
                >
                    @if (! $notifications->onFirstPage())
                        <x-filament::button
                            color="gray"
                            rel="prev"
                            :wire:click="'previousPage(\'' . $notifications->getPageName() . '\')'"
                            class="me-auto"
                        >
                            {{ __('filament-notifications::database.modal.pagination.actions.previous.label') }}
                        </x-filament::button>
                    @endif

                    @if ($notifications->hasMorePages())
                        <x-filament::button
                            color="gray"
                            rel="next"
                            :wire:click="'nextPage(\'' . $notifications->getPageName() . '\')'"
                            class="ms-auto"
                        >
                            {{ __('filament-notifications::database.modal.pagination.actions.next.label') }}
                        </x-filament::button>
                    @endif
                </nav>
            </x-slot>
        @endif
    @endif
</x-filament::modal>
