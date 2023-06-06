@props([
    'notifications',
    'unreadNotificationsCount',
])

<div {{ $attributes }}>
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
                    {{ __('notifications::database.modal.buttons.mark_all_as_read.label') }}
                </x-notifications::link>

                <span>&bull;</span>
            @endif

            <x-notifications::link
                wire:click="clearDatabaseNotifications"
                x-on:click="close()"
                color="secondary"
                tag="button"
                tabindex="-1"
                wire:target="clearDatabaseNotifications"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-70 cursor-wait"
            >
                {{ __('notifications::database.modal.buttons.clear.label') }}
            </x-notifications::link>
        </div>
    @endif
</div>
