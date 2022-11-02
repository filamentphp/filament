@props([
    'darkMode' => false,
])

<p {{ $attributes->class(['filament-modal-subheading text-gray-500']) }}>
    {{ $slot }}
</p>
