@props([
    'actions' => null,
    'heading',
    'subheading' => null,
])

<header
    {{ $attributes->class(['fi-header items-start justify-between gap-y-2 sm:flex sm:gap-x-4 sm:gap-y-0 sm:py-4']) }}
>
    <div>
        <x-filament::header.heading>
            {{ $heading }}
        </x-filament::header.heading>

        @if ($subheading)
            <x-filament::header.subheading class="mt-1">
                {{ $subheading }}
            </x-filament::header.subheading>
        @endif
    </div>

    <x-filament-actions::actions :actions="$actions" class="shrink-0" />
</header>
