@props([
    'alias',
    'class' => [],
    'color' => null,
    'name',
    'size',
])

@php
    $customIcon = \Filament\Support\Facades\Icon::resolve($alias);

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

@svg($name, \Illuminate\Support\Arr::toCssClasses($class), $attributes->getAttributes())
