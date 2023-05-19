@props([
    'color' => null,
    'name',
    'size' => 'lg',
])

<x-filament::icon
    :name="$name"
    alias="filament-notifications::notification"
    :color="match ($color) {
        'danger' => 'text-danger-400',
        'gray', null => 'text-gray-400',
        'info' => 'text-info-400',
        'primary' => 'text-primary-400',
        'secondary' => 'text-secondary-400',
        'success' => 'text-success-400',
        'warning' => 'text-warning-400',
        default => $color,
    }"
    :size="match ($size) {
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
        'lg' => 'h-6 w-6',
        default => $size,
    }"
    class="filament-notifications-notification-icon"
/>
