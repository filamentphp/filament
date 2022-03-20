@props([
    'columns' => '3',
])

@php
    $columns = (int) $columns;
@endphp

<div {{ $attributes->class([
    'grid gap-4 lg:gap-8 filament-stats',
    'md:grid-cols-3' => $columns === 3,
    'md:grid-cols-1' => $columns === 1,
    'md:grid-cols-2' => $columns === 2,
    'md:grid-cols-2 xl:grid-cols-4' => $columns === 4,
]) }}>
    {{ $slot }}
</div>
