@props([
    'alias' => null,
    'class' => [],
    'color' => null,
    'group' => null,
    'name' => null,
    'size',
])

@php
    $icon = $alias ? \Filament\Support\Facades\FilamentIcon::resolve($alias) : null;
    $group = $group ? \Filament\Support\Facades\FilamentIcon::resolve($group) : null;

    if ($icon?->name) {
        $name = $icon->name;
    }

    $class = array_merge(
        $group?->class ?? [],
        $icon?->class ?? [],
        Arr::wrap($class),
    );

    $color = $icon?->color ?? $group?->color ?? $color;

    if ($color !== null) {
        $class[] = $color;
    }

    $class[] = $icon?->size ?? $group?->size ?? $size;
@endphp

@if ($name)
    @svg($name, \Illuminate\Support\Arr::toCssClasses($class), array_filter($attributes->getAttributes()))
@else
    <div {{ $attributes->class($class) }}>
        {{ $slot }}
    </div>
@endif
