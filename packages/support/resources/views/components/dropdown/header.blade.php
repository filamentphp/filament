@props([
    'color' => 'primary',
    'icon' => null,
    'tag' => 'div',
])

<{{ $tag }} {{ $attributes->class(['filament-dropdown-header flex w-full p-3 text-sm']) }}>
    @if ($icon)
        <x-filament::icon
            :name="$icon"
            alias="support::dropdown.header"
            :color="match ($color) {
                'danger' => 'text-danger-500',
                'gray' => 'text-gray-500',
                'primary' => 'text-primary-500',
                'secondary' => 'text-secondary-500',
                'success' => 'text-success-500',
                'warning' => 'text-warning-500',
                default => $color,
            }"
            size="h-5 w-5"
            class="filament-dropdown-header-icon mr-2 rtl:ml-2 rtl:mr-0"
        />
    @endif

    <span class="filament-dropdown-header-label">
        {{ $slot }}
    </span>
</{{ $tag }}>
