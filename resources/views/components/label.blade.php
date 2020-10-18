@props([
    'for' => null,
    'hidden' => false,
])

<label for="{{ $for }}" {{ $attributes->merge(['class' => 'text-gray-700 text-sm font-bold'.($hidden ? ' sr-only' : '')]) }}>
    {{ $slot }}
</label>