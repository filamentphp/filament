@props([
    'actions' => null,
    'heading',
])

<header {{ $attributes->class('space-y-2 items-start justify-between sm:flex sm:space-y-0 sm:space-x-4 sm:py-4') }}>
    <x-filament::header.heading>
        {{ $heading }}
    </x-filament::header.heading>

    <x-filament::actions :actions="$actions" class="shrink-0" />
</header>
