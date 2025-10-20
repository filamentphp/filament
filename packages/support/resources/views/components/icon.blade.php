@props([
    'alias' => null,
    'icon' => null,
    'size' => null,
])

{{ \Filament\Support\generate_icon_html($icon, $alias, $attributes, $size) }}
