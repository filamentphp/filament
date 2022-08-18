@props([
    'notification',
])

<div
    {{ $attributes->class('filament-notifications-notification pointer-events-auto') }}
    x-data="notificationComponent({
        $wire: $wire,
        notification: {{ \Illuminate\Support\Js::from($notification->toArray()) }},
    })"
    wire:key="{{ $this->id }}.notifications.{{ $notification->getId() }}"
    dusk="filament.notifications.notification"
>
    {{ $slot }}
</div>
