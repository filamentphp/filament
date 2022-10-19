@props([
    'icon',
    'color',
])

<x-filament-support::icon
    :name="$icon"
    :class="\Illuminate\Support\Arr::toCssClasses([
        'filament-notifications-icon h-6 w-6',
        match ($color) {
            'success' => 'text-success-400',
            'warning' => 'text-warning-400',
            'danger' => 'text-danger-400',
            'primary' => 'text-primary-400',
            'secondary' => 'text-gray-400',
        },
    ])"
/>
