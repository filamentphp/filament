@props([
    'notifications',
    'unreadNotificationsCount',
    'markAllNotificationsAsReadAction',
])

<div {{ $attributes->class('mt-2 flex gap-x-3') }}>
    @if ($unreadNotificationsCount && $markAllNotificationsAsReadAction)
        {{ ($markAllNotificationsAsReadAction)->isVisible() ? $markAllNotificationsAsReadAction : '' }}
    @endif

    <x-filament::link
        color="danger"
        tabindex="-1"
        tag="button"
        wire:click="clearNotifications"
        x-on:click="close()"
    >
        {{ __('filament-notifications::database.modal.actions.clear.label') }}
    </x-filament::link>
</div>
