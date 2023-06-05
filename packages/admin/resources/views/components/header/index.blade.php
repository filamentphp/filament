@props([
    'actions' => null,
    'heading',
    'subheading' => null,
])

<header
    {{ $attributes->class(['filament-header items-start justify-between space-y-2 sm:flex sm:space-x-4 sm:space-y-0 sm:py-4 sm:rtl:space-x-reverse']) }}
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

    <x-filament::pages.actions :actions="$actions" class="shrink-0" />
</header>
