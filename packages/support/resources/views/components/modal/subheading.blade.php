@props([
    'darkMode' => false,
])

<h3 {{ $attributes->class(['filament-modal-subheading text-gray-500']) }}>
    {{ $slot }}
</h3>
