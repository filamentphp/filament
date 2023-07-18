@props([
    'alias' => null,
    'class' => '',
    'name' => null,
])

@php
    $name = ($alias ? \Filament\Support\Facades\FilamentIcon::resolve($alias) : null) ?: $name;
@endphp

@if ($name)
    @svg($name, $class, array_filter($attributes->getAttributes()))
@else
    <div {{ $attributes->class($class) }}>
        {{ $slot }}
    </div>
@endif
