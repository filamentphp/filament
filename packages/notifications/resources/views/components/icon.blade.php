@props([
    'color' => 'gray',
    'icon',
    'size' => 'lg',
])

<x-filament::icon
    :icon="$icon"
    :attributes="
        $attributes
            ->class([
                'fi-no-notification-icon text-custom-400',
                match ($size) {
                    'sm' => 'h-4 w-4',
                    'md' => 'h-5 w-5',
                    'lg' => 'h-6 w-6',
                    default => $size,
                },
            ])
            ->style([
                \Filament\Support\get_color_css_variables($color, shades: [400]),
            ])
    "
/>
