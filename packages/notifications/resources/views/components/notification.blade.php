<div
    {{ $attributes }}
    x-ref="notification"
    x-show="isVisible"
    dusk="filament.notifications.notification"
>
    {{ $slot }}
</div>
