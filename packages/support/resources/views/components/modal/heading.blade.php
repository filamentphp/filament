@props([
    'darkMode' => false,
])

<h2 {{ $attributes->class(['text-xl font-bold tracking-tight filament-modal-heading']) }}>
    {{ $slot }}
</h2>
