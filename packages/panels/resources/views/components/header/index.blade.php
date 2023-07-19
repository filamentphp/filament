@props([
    'actions' => null,
    'heading',
    'subheading' => null,
])

<header
    {{ $attributes->class(['fi-header flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between']) }}
>
    <div>
        <x-filament::header.heading>
            {{ $heading }}
        </x-filament::header.heading>

        @if ($subheading)
            <x-filament::header.subheading class="mt-2">
                {{ $subheading }}
            </x-filament::header.subheading>
        @endif
    </div>

    <x-filament-actions::actions :actions="$actions" class="shrink-0" />
</header>
