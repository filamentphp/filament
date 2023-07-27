@props([
    'tag' => 'td',
])

<{{ $tag }}
    {{ $attributes->class(['fi-ta-cell p-0 sm:first-of-type:ps-3 sm:last-of-type:pe-3']) }}
>
    {{ $slot }}
</{{ $tag }}>
