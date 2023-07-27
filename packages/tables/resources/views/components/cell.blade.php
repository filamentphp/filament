@props([
    'tag' => 'td',
])

<{{ $tag }}
    {{ $attributes->class(['fi-ta-cell first-of-type:ps-4']) }}
>
    {{ $slot }}
</{{ $tag }}>
