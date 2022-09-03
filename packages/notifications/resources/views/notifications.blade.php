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

    @if (config('notifications.database'))
        <x-notifications::modal
            id="notifications"
            close-button
            slide-over
            width="md"
        >
            @php
                $notifications = auth()->user()->notifications;
            @endphp

            @if ($notifications->count())
                <x-slot name="header">
                    <x-notifications::modal.heading class="relative">
                        <span>
                            Notifications
                        </span>

                        @if ($count = auth()->user()->unreadNotifications()->count())
                            <span @class([
                                'inline-flex absolute items-center justify-center top-0 ml-1 min-w-[1rem] h-4 rounded-full text-xs text-primary-700 bg-primary-500/10',
                                'dark:text-primary-500' => config('tables.dark_mode'),
                            ])>
                                {{ $count }}
                            </span>
                        @endif
                    </x-notifications::modal.heading>

                    @if ($notifications->count())
                        <div class="mt-2 text-sm">
                            <x-notifications::link
                                wire:click="markAllAsRead"
                                color="secondary"
                                tag="button"
                                tabindex="-1"
                            >
                                Mark all as read
                            </x-notifications::link>

                            &bull;

                            <x-notifications::link
                                wire:click="clear"
                                color="secondary"
                                tag="button"
                                tabindex="-1"
                            >
                                Clear
                            </x-notifications::link>
                        </div>
                    @endif
                </x-slot>

                <div class="-mt-4">
                    @foreach ($notifications as $notification)
                        <div class="-mx-6 border-b">
                            <div @class([
                                'p-4',
                                'bg-gray-50' => $notification->unread(),
                            ])>
                                {{ \Filament\Notifications\Notification::fromDatabase($notification)->inline() }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div @class([
                    'flex flex-col items-center justify-center mx-auto space-y-4 text-center bg-white',
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
