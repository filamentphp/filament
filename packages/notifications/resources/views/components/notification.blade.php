@props([
    'notification',
])

<div
    x-data="notificationComponent({ notification: @js($notification) })"
    {{
        $attributes
            ->merge([
                'dusk' => 'filament.notifications.notification',
                'wire:key' => "{$this->id}.notifications.{$notification->getId()}",
            ], escape: false)
            ->class(['filament-notifications-notification pointer-events-auto invisible'])
    }}
>
    {{ $slot }}
</div>
