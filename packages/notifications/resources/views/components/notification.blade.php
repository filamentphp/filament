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
            ], escape: true)
            ->class(['filament-notifications-notification pointer-events-auto invisible'])
    }}
>
    {{ $slot }}
</div>
