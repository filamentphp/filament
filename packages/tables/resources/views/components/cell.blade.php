@props([
    'tag' => 'td',
])

<{{ $tag }}
    {{ $attributes->class(['fi-ta-cell sm:first-of-type:ps-6']) }}
>
    {{ $slot }}
</{{ $tag }}>
