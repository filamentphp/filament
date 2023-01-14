<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    @php
        $size = $getSize() ?? 'lg';
        $color = $getColor();
        $icon = $getIcon();
    @endphp

    <div {{ $attributes
        ->merge($getExtraAttributes(), escape: false)
        ->class([
            'filament-infolists-icon-entry filament-tables-icon-column-size-' . str($size)->kebab(),
        ])
    }}>
        @if ($icon)
            <x-filament::icon
                :name="$icon"
                alias="filament-infolists::components.icon-entry"
                :color="match ($color) {
                    'danger' => 'text-danger-500',
                    'gray', null => 'text-gray-500',
                    'primary' => 'text-primary-500',
                    'secondary' => 'text-secondary-500',
                    'success' => 'text-success-500',
                    'warning' => 'text-warning-500',
                    default => $color,
                }"
                :size="match ($size) {
                    'xs' => 'h-3 w-3',
                    'sm' => 'h-4 w-4',
                    'md' => 'h-5 w-5',
                    'lg' => 'h-6 w-6',
                    'xl' => 'h-7 w-7',
                    default => $size,
                }"
            />
        @endif
    </div>
</x-dynamic-component>
