@props([
    'alias' => null,
    'class' => '',
    'icon' => null,
])

@php
    $icon = ($alias ? \Filament\Support\Facades\FilamentIcon::resolve($alias) : null) ?: $icon;
@endphp

@if (is_string($icon) && !str_contains($icon, '/'))
    @svg($icon, $class, array_filter($attributes->getAttributes()))
@elseif(is_string($icon) && str_contains($icon, '/'))
    <img {{ $attributes->class($class) }} src="{{ $icon }}" />
@else
    <div {{ $attributes->class($class) }}>
        {{ $icon ?? $slot }}
    </div>
@endif
