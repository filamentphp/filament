@props([
    'alias' => null,
    'icon' => null,
])

{{ \Filament\Support\generate_icon_html($icon, $alias, $attributes) }}
