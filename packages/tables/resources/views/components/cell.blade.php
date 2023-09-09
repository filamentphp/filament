@props([
    'tag' => 'td',
])

<{{ $tag }}
    {{ $attributes->class(['fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3']) }}
>
    {{ $slot }}
</{{ $tag }}>
