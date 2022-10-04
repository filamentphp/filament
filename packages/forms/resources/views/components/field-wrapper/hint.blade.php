@props([
    'icon' => null,
    'color' => null,
])

<div {{ $attributes->class(
    array_merge(
        [
            'filament-forms-field-wrapper-hint flex space-x-2 rtl:space-x-reverse',
        ],
        match ($color) {
            'danger' => [
                'text-danger-700',
                'dark:text-danger-500' => config('tables.dark_mode'),
            ],
            'secondary' => [
                'text-gray-700',
                'dark:text-gray-500' => config('tables.dark_mode'),
            ],
            'success' => [
                'text-success-700',
                'dark:text-success-500' => config('tables.dark_mode'),
            ],
            'warning' => [
                'text-warning-700',
                'dark:text-warning-500' => config('filament.dark_mode'),
            ],
            'primary' => [
                'text-primary-700',
                'dark:text-primary-500' => config('tables.dark_mode'),
            ],
            null => [
                'text-gray-500',
                'dark:text-gray-300' => config('tables.dark_mode'),
            ],
        },
    )
) }}>
    @if ($slot->isNotEmpty())
        <span class="text-xs leading-tight">
            {{ $slot }}
        </span>
    @endif

    @if ($icon)
        <x-dynamic-component :component="$icon" class="h-4 w-4" />
    @endif
</div>
