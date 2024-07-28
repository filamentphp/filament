@props([
    'notification',
])

<div
    x-data="notificationComponent({ notification: @js($notification) })"
    {{
        $attributes
            ->merge([
                'wire:key' => "{$this->getId()}.notifications.{$notification->getId()}",
                'x-on:close-notification.window' => "if (\$event.detail.id == '{$notification->getId()}') close()",
            ], escape: false)
            ->class(['pointer-events-auto invisible'])
    }}
>
    {{ $slot }}
</div>
