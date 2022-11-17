@php
    $size = $getSize() ?? 'lg';
    $color = $getColor();
    $icon = $getIcon();
@endphp

<div {{ $attributes
    ->merge($getExtraAttributes(), escape: true)
    ->class([
        'filament-tables-icon-column filament-tables-icon-column-size-' . str($size)->kebab(),
        'px-4 py-3' => ! $isInline(),
    ])
}}>
    @if ($icon)
        <x-filament::icon
            :name="$icon"
            alias="filament-tables::columns.icon"
            :color="match ($color) {
                'danger' => 'text-danger-500',
                'primary' => 'text-primary-500',
                'secondary' => 'text-secondary-500',
                'success' => 'text-success-500',
                'warning' => 'text-warning-500',
                null => 'text-gray-500',
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
