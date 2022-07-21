@props([
    'notification',
])

<div
    {{ $attributes }}
    x-data="notificationComponent({ notification: {{ \Illuminate\Support\Js::from($notification->toArray()) }} })"
    x-show="isVisible"
    wire:key="notification-{{ $notification->getId() }}"
    dusk="filament.notifications.notification"
>
    {{ $slot }}
</div>
