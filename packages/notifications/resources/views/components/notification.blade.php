@props([
    'notification',
])

<div
    x-data="notificationComponent({ notification: @js($notification) })"
    {{
        $attributes
            ->merge([
                'wire:key' => "{$this->getId()}.notifications.{$notification->getId()}",
            ], escape: false)
            ->class(['fi-no-notification pointer-events-auto invisible'])
    }}
>
    {{ $slot }}
</div>
