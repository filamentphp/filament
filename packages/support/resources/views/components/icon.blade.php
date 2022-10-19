@props([
    'alias',
    'class' => [],
    'color',
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

    if ($customIcon?->color !== null) {
        $color = $customIcon->color;
    }

    $class[] = $color;

    if ($customIcon?->size !== null) {
        $size = $customIcon->size;
    }

    $class[] = $size;
@endphp

@svg($name, \Illuminate\Support\Arr::toCssClasses($class), $attributes)
