@props([
    'notification',
])

<div
    {{ $attributes }}
    x-data="notificationComponent({
        $wire: $wire,
        notification: {{ \Illuminate\Support\Js::from($notification->toArray()) }},
    })"
    x-show="!isEntering"
    x-bind:class="{ 'opacity-0': isClosing }"
    wire:key="notification-{{ $notification->getId() }}"
    dusk="filament.notifications.notification"
>
    {{ $slot }}
</div>
