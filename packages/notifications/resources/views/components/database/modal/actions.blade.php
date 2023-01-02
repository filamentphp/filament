@props([
    'notifications',
    'unreadNotificationsCount',
])

<div {{ $attributes }}>
    @if ($notifications->count())
        <div class="mt-2 text-sm">
            @if ($unreadNotificationsCount)
                <x-filament::link
                    wire:click="markAllDatabaseNotificationsAsRead"
                    color="gray"
                    tag="button"
                    tabindex="-1"
                    wire:target="markAllDatabaseNotificationsAsRead"
                    wire:loading.attr="disabled"
                    class="disabled:opacity-70 disabled:pointer-events-none"
                >
                    {{ __('filament-notifications::database.modal.buttons.mark_all_as_read.label') }}
                </x-filament::link>

                <span>
                    &bull;
                </span>
            @endif

            <x-filament::link
                wire:click="clearDatabaseNotifications"
                x-on:click="close()"
                color="gray"
                tag="button"
                tabindex="-1"
                wire:target="clearDatabaseNotifications"
                wire:loading.attr="disabled"
                class="disabled:opacity-70 disabled:pointer-events-none"
            >
                {{ __('filament-notifications::database.modal.buttons.clear.label') }}
            </x-filament::link>
        </div>
    @endif
</div>
