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
    :icon="$hasNotifications ? null : 'heroicon-o-bell'"
    :icon-alias="$hasNotifications ? null : 'notifications::database.modal.empty-state'"
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

        <div class="-mx-6 divide-y border-b dark:border-gray-700">
            @foreach ($notifications as $notification)
                <div
                    @class([
                        'bg-primary-50 dark:bg-gray-700' => $notification->unread(),
                    ])
                >
                    {{ $this->getNotification($notification)->inline() }}
                </div>
            @endforeach
        </div>

        @if ($notifications instanceof \Illuminate\Contracts\Pagination\Paginator && $notifications->hasPages())
            <x-slot name="footer">
                @php
                    $isRtl = __('filament::layout.direction') === 'rtl';
                    $previousArrowIcon = $isRtl ? 'heroicon-m-chevron-right' : 'heroicon-m-chevron-left';
                    $nextArrowIcon = $isRtl ? 'heroicon-m-chevron-left' : 'heroicon-m-chevron-right';
                @endphp

                <nav
                    aria-label="{{ __('filament-notifications::database.modal.pagination.label') }}"
                    role="navigation"
                    class="flex items-center justify-between"
                >
                    <div class="flex items-center">
                        @if (! $notifications->onFirstPage())
                            <x-filament::button
                                color="gray"
                                :icon="$previousArrowIcon"
                                rel="prev"
                                size="sm"
                                :wire:click="'previousPage(\'' . $notifications->getPageName() . '\')'"
                            >
                                {{ __('filament-notifications::database.modal.pagination.actions.previous.label') }}
                            </x-filament::button>
                        @endif
                    </div>

                    <div class="flex items-center">
                        @if ($notifications->hasMorePages())
                            <x-filament::button
                                color="gray"
                                :icon="$nextArrowIcon"
                                icon-position="after"
                                rel="next"
                                size="sm"
                                :wire:click="'nextPage(\'' . $notifications->getPageName() . '\')'"
                            >
                                {{ __('filament-notifications::database.modal.pagination.actions.next.label') }}
                            </x-filament::button>
                        @endif
                    </div>
                </nav>
            </x-slot>
        @endif
    @endif
</x-filament::modal>
