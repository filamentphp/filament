@props([
    'notification',
])

<div
    {{ $attributes->class(['filament-notifications-notification pointer-events-auto invisible']) }}
    x-data="notificationComponent({ notification: @js($notification) })"
    wire:key="{{ $this->id }}.notifications.{{ $notification->getId() }}"
    dusk="filament.notifications.notification"
>
    {{ $slot }}
</div>
