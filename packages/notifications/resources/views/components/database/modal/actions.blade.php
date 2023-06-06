@props([
    'notifications',
    'unreadNotificationsCount',
])

<div {{ $attributes }}>
    @if ($notifications->count())
        <div class="mt-2 text-sm">
            @if ($unreadNotificationsCount)
                <x-filament::link
                    wire:click="markAllNotificationsAsRead"
                    color="gray"
                    tag="button"
                    tabindex="-1"
                    wire:target="markAllNotificationsAsRead"
                    wire:loading.attr="disabled"
                    class="disabled:pointer-events-none disabled:opacity-70"
                >
                    {{ __('filament-notifications::database.modal.buttons.mark_all_as_read.label') }}
                </x-filament::link>

                <span>&bull;</span>
            @endif

            <x-filament::link
                wire:click="clearNotifications"
                x-on:click="close()"
                color="gray"
                tag="button"
                tabindex="-1"
                wire:target="clearNotifications"
                wire:loading.attr="disabled"
                class="disabled:pointer-events-none disabled:opacity-70"
            >
                {{ __('filament-notifications::database.modal.buttons.clear.label') }}
            </x-filament::link>
        </div>
    @endif
</div>
