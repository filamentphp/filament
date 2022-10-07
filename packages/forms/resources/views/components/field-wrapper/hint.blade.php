@props([
    'action' => null,
    'color' => null,
    'icon' => null,
])

<div {{ $attributes->class(array_merge(
    ['filament-forms-field-wrapper-hint flex items-center space-x-2 rtl:space-x-reverse'],
    match ($color) {
        'danger' => [
            'text-danger-500',
            'dark:text-danger-300' => config('tables.dark_mode'),
        ],
        'success' => [
            'text-success-500',
            'dark:text-success-300' => config('tables.dark_mode'),
        ],
        'warning' => [
            'text-warning-500',
            'dark:text-warning-300' => config('filament.dark_mode'),
        ],
        'primary' => [
            'text-primary-500',
            'dark:text-primary-300' => config('tables.dark_mode'),
        ],
        default => [
            'text-gray-500',
            'dark:text-gray-300' => config('tables.dark_mode'),
        ],
    },
)) }}>
    @if ($slot->isNotEmpty())
        <span class="text-xs leading-tight">
            {{ $slot }}
        </span>
    @endif

    @if ($icon)
        <x-dynamic-component :component="$icon" class="h-4 w-4" />
    @endif

    @if ($action && (! $action->isHidden()))
        <div class="filament-forms-field-wrapper-hint-action">
            {{ $action }}
        </div>
    @endif
</div>
