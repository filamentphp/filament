@props([
    'alias' => null,
    'class' => '',
    'icon' => null,
])

@php
    $icon = ($alias ? \Filament\Support\Facades\FilamentIcon::resolve($alias) : null) ?: $icon;
@endphp

@if (is_string($icon))
    @svg($icon, $class, array_filter($attributes->getAttributes()))
@else
    <div {{ $attributes->class($class) }}>
        {{ $icon ?? $slot }}
    </div>
@endif
