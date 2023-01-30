@props([
    'color' => 'primary',
    'icon' => null,
    'tag' => 'div',
])

<{{ $tag }} {{ $attributes->class([
    'filament-dropdown-header flex w-full gap-2 p-3 text-sm',
    match ($color) {
        'danger' => 'filament-dropdown-header-color-danger text-danger-600 dark:text-danger-400',
        'gray' => 'filament-dropdown-header-color-gray text-gray-700 dark:text-gray-200',
        'primary' => 'filament-dropdown-header-color-primary text-primary-600 dark:text-primary-400',
        'secondary' => 'filament-dropdown-header-color-secondary text-secondary-600 dark:text-secondary-400',
        'success' => 'filament-dropdown-header-color-success text-success-600 dark:text-success-400',
        'warning' => 'filament-dropdown-header-color-warning text-warning-600 dark:text-warning-400',
        default => $color,
    },
]) }}>
    @if ($icon)
        <x-filament::icon
            :name="$icon"
            alias="support::dropdown.header"
            size="h-5 w-5"
            class="filament-dropdown-header-icon"
        />
    @endif

    <span class="filament-dropdown-header-label">
        {{ $slot }}
    </span>
</{{ $tag }}>
