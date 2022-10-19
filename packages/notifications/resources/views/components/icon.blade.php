@props([
    'icon',
    'color',
])

<x-filament-support::icon
    :name="$icon"
    alias="notifications::notification"
    size="h-6 w-6"
    :class="\Illuminate\Support\Arr::toCssClasses([
        'filament-notifications-notification-icon',
        match ($color) {
            'success' => 'text-success-400',
            'warning' => 'text-warning-400',
            'danger' => 'text-danger-400',
            'primary' => 'text-primary-400',
            'secondary' => 'text-gray-400',
        },
    ])"
/>
