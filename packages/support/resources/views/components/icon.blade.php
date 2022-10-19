@props([
    'class' => null,
    'name',
])

{{ \Filament\Support\Facades\Icon::render($name, $class, $attributes) }}
