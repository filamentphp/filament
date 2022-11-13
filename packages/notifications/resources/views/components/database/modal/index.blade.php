@props(['notifications', 'unreadNotificationsCount'])

<x-notifications::modal id="database-notifications" close-button slide-over width="md">
    @if ($notifications->count())
        <x-slot name="header">
            <x-notifications::database.modal.heading :unread-notifications-count="$unreadNotificationsCount" />

            <x-notifications::database.modal.actions :notifications="$notifications" :unread-notifications-count="$unreadNotificationsCount" />
        </x-slot>
        @foreach ($notifications as $key => $notificationGroup)
            <div x-data="{ isOpen: true }" class="filament-sidebar-group">
                @if ($key)
                    <button class="flex items-center justify-between w-full" x-on:click.prevent="isOpen= !isOpen">
                        <div @class([
                            'flex items-center gap-4 text-gray-600',
                            'dark:text-gray-300' => config('filament.dark_mode'),
                        ])>


                            <p class="flex-1 text-xs font-bold tracking-wider uppercase">
                                {{ $key }}
                            </p>
                        </div>


                        <x-heroicon-o-chevron-down :class="\Illuminate\Support\Arr::toCssClasses([
                            'w-3 h-3 text-gray-600 transition',
                            'dark:text-gray-300' => config('filament.dark_mode'),
                        ])" x-bind:class="isOpen || '-rotate-180'" x-cloak />

                    </button>
                @endif

                    <div x-show="isOpen" x-collapse.duration.200ms @class(['text-sm space-y-1 -mx-3', 'mt-2' => $key])>


                        @foreach ($notificationGroup as $notification)
                            <div @class([
                                'border  rounded-lg overflow-hidden ',
                                'dark:border-gray-700' =>
                                    !$notification->unread() && config('notifications.dark_mode'),
                                'dark:border-gray-800' =>
                                    $notification->unread() && config('notifications.dark_mode'),
                            ])>
                                <div @class([
                                    'py-2 pl-4 pr-2',
                                    'bg-primary-50' => $notification->unread(),
                                    'dark:bg-gray-700' =>
                                        $notification->unread() && config('notifications.dark_mode'),
                                ])>
                                    {{ $this->getNotificationFromDatabaseRecord($notification)->inline() }}
                                </div>
                            </div>
                        @endforeach

                    </div>

            </div>
            @if (! $loop->last)

            <div @class([
                'border-t -mx-6',
                'dark:border-gray-700' => config('filament.dark_mode'),
            ])></div>

    @endif
        @endforeach
    @else
        <x-notifications::database.modal.empty-state />
    @endif
</x-notifications::modal>
