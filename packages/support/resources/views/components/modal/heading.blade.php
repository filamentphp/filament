@props([
    'darkMode' => false,
])

<h2
    {{ $attributes->class(['filament-modal-heading text-xl font-bold tracking-tight']) }}
>
    {{ $slot }}
</h2>
