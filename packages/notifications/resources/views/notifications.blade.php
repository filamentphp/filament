<div>
    <div
        @class([
            'filament-notifications pointer-events-none fixed inset-4 z-50 mx-auto flex justify-end gap-3',
            match (config('notifications.layout.alignment.horizontal')) {
                'left' => 'items-start',
                'center' => 'items-center',
                'right' => 'items-end',
            },
            match (config('notifications.layout.alignment.vertical')) {
                'top' => 'flex-col-reverse',
                'bottom' => 'flex-col',
            },
        ])
        role="status"
    >
        @foreach ($notifications as $notification)
            {{ $notification }}
        @endforeach
    </div>

    @if ($this->hasDatabaseNotifications())
        @php
            $notifications = $this->getDatabaseNotifications();
            $unreadNotificationsCount = $this->getUnreadDatabaseNotificationsCount();
        @endphp

        <div
            @if ($pollingInterval = $this->getPollingInterval())
                wire:poll.{{ $pollingInterval }}
            @endif
        >
            @if ($databaseNotificationsButton = $this->getDatabaseNotificationsButton())
                <div x-on:click="$dispatch('open-modal', { id: 'notifications' })" class="inline-block">
                    {{ $databaseNotificationsButton->with(['unreadNotificationsCount' => $unreadNotificationsCount]) }}
                </div>
            @endif

            <x-notifications::modal
                id="notifications"
                close-button
                slide-over
                width="md"
            >
                @if ($notifications->count())
                    <x-slot name="header">
                        <x-notifications::modal.heading class="relative">
                            <span>
                                Notifications
                            </span>

                            @if ($unreadNotificationsCount)
                                <span @class([
                                    'inline-flex absolute items-center justify-center top-0 ml-1 min-w-[1rem] h-4 rounded-full text-xs text-primary-700 bg-primary-500/10',
                                    'dark:text-primary-500' => config('tables.dark_mode'),
                                ])>
                                    {{ $unreadNotificationsCount }}
                                </span>
                            @endif
                        </x-notifications::modal.heading>

                        @if ($notifications->count())
                            <div class="mt-2 text-sm">
                                @if ($unreadNotificationsCount)
                                    <x-notifications::link
                                        wire:click="markAllDatabaseNotificationsAsRead"
                                        color="secondary"
                                        tag="button"
                                        tabindex="-1"
                                        wire:target="markAllDatabaseNotificationsAsRead"
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-70 cursor-wait"
                                    >
                                        Mark all as read
                                    </x-notifications::link>

                                    &bull;
                                @endif

                                <x-notifications::link
                                    wire:click="clearDatabaseNotifications"
                                    color="secondary"
                                    tag="button"
                                    tabindex="-1"
                                    wire:target="clearDatabaseNotifications"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-70 cursor-wait"
                                >
                                    Clear
                                </x-notifications::link>
                            </div>
                        @endif
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
                    <div @class([
                        'flex flex-col items-center justify-center mx-auto my-6 space-y-4 text-center bg-white',
                        'dark:bg-gray-800' => config('notifications.dark_mode'),
                    ])>
                        <div @class([
                            'flex items-center justify-center w-12 h-12 text-primary-500 rounded-full bg-primary-50',
                            'dark:bg-gray-700' => config('notifications.dark_mode'),
                        ])>
                            <x-heroicon-o-bell class="w-5 h-5" />
                        </div>

                        <div class="max-w-md space-y-1">
                            <h2 @class([
                                'text-lg font-bold tracking-tight',
                                'dark:text-white' => config('notifications.dark_mode'),
                            ])>
                                No notifications here
                            </h2>

                            <p @class([
                                'whitespace-normal text-sm font-medium text-gray-500',
                                'dark:text-gray-400' => config('notifications.dark_mode'),
                            ])>
                                Please check again later
                            </p>
                        </div>
                    </div>
                @endif
            </x-notifications::modal>
        @endif
    </div>

    <div
        x-data="{}"
        x-init="
            window.addEventListener('EchoLoaded', () => {
                window.Echo.private(@js($this->getBroadcastChannel()))
                    .notification((notification) => {
                        setTimeout(() => $wire.handleBroadcastNotification(notification), 500)
                    })
                    .listen('.database-notifications.sent', () => {
                        setTimeout(() => $wire.call('$refresh'), 500)
                    })
            })

            if (window.Echo) {
                new CustomEvent('EchoLoaded')
            }
        "
    ></div>
</div>
