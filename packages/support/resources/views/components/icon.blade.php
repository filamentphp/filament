@props([
    'alias',
    'class' => [],
    'color' => null,
    'name' => null,
    'size',
])

@php
    $customIcon = \Filament\Support\Facades\FilamentIcon::resolve($alias);

    if ($customIcon) {
        $name = $customIcon->name;
    }

    $class = array_merge(
        Arr::wrap($class),
        $customIcon?->class ?? [],
    );

    $color = $customIcon?->color ?? $color;

    if ($color !== null) {
        $class[] = $color;
    }

    $class[] = $customIcon?->size ?? $size;
@endphp

@if ($name)
    @svg($name, \Illuminate\Support\Arr::toCssClasses($class), array_filter($attributes->getAttributes()))
@else
    <div {{ $attributes->class($class) }}>
        {{ $slot }}
    </div>
@endif
