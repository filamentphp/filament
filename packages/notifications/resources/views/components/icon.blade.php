@props([
    'color' => 'gray',
    'name',
    'size' => 'lg',
])

<x-filament::icon
    :name="$name"
    alias="notifications::notification"
    color="text-custom-400"
    :size="
        match ($size) {
            'sm' => 'h-4 w-4',
            'md' => 'h-5 w-5',
            'lg' => 'h-6 w-6',
            default => $size,
        }
    "
    :attributes="
        $attributes
            ->class(['filament-notifications-notification-icon'])
            ->style([
                \Filament\Support\get_color_css_variables($color, shades: [400]),
            ])
    "
/>
