@props([
    'type' => 'button',
])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'btn']) }}>
    {{ $slot }}
</button>
