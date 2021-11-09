@props([
    'actions' => [],
    'heading',
])

<header {{ $attributes->class('space-y-2 items-center justify-between sm:flex sm:space-y-0 sm:space-x-4 sm:py-4') }}>
    <h1 class="text-2xl font-bold tracking-tight md:text-3xl">
        {{ $heading }}
    </h1>

    <x-filament::actions :actions="$actions" />
</header>
