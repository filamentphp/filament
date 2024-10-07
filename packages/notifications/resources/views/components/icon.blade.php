@php
    use Filament\Support\Enums\IconSize;
@endphp

@props([
    'color' => 'gray',
    'icon',
    'size' => IconSize::Large,
])

<x-filament::icon
    :icon="$icon"
    :attributes="
        $attributes
            ->class([
                'fi-no-notification-icon',
                match ($color) {
                    'gray' => 'text-gray-400',
                    default => 'fi-color-custom text-custom-400',
                },
                is_string($color) ? 'fi-color-' . $color : null,
                match ($size) {
                    IconSize::Small, 'sm' => 'size-4',
                    IconSize::Medium, 'md' => 'size-5',
                    IconSize::Large, 'lg' => 'size-6',
                    default => $size,
                },
            ])
            ->style([
                \Filament\Support\get_color_css_variables(
                    $color,
                    shades: [400],
                    alias: 'notifications::notification.icon',
                ),
            ])
    "
/>
