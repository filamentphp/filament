@props([
    'actions' => null,
    'heading',
])

<header {{ $attributes->class('space-y-2 items-start justify-between sm:flex sm:space-y-0 sm:space-x-4  sm:rtl:space-x-reverse sm:py-4 filament-header') }}>
    <x-filament::header.heading>
        {{ $heading }}
    </x-filament::header.heading>

    <x-filament::pages.actions :actions="$actions" class="shrink-0" />
</header>
