@props([
    'tag' => 'div',
])

<{{ $tag }} {{ $attributes->merge(['class' => 'bg-white shadow-xl rounded p-4 md:p-6']) }}>
    
    {{ $slot }}
</{{ $tag }}>