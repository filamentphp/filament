@props([
    'notification',
])

<div
    {{ $attributes->class('filament-notifications-notification pointer-events-auto') }}
    x-data="notificationComponent({
        $wire: $wire,
        notification: {{ \Illuminate\Support\Js::from($notification->toArray()) }},
    })"
    x-show="phase !== 'enter-start'"
    x-bind:class="{
        [$el.getAttribute('x-transition:leave')]: phase.startsWith('leave-'),
        [$el.getAttribute('x-transition:leave-start')]: phase === 'leave-start',
        [$el.getAttribute('x-transition:leave-end')]: phase === 'leave-end',
    }"
    wire:key="notification-{{ $notification->getId() }}"
    dusk="filament.notifications.notification"
>
    {{ $slot }}
</div>
