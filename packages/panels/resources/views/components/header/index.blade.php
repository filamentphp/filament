@props([
    'actions' => null,
    'breadcrumbs' => [],
    'heading',
    'subheading' => null,
])

<header
    {{ $attributes->class(['fi-header flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between']) }}
>
    <div>
        @if ($breadcrumbs)
            <x-filament::breadcrumbs
                :breadcrumbs="$breadcrumbs"
                class="mb-2 hidden sm:block"
            />
        @endif

        <x-filament::header.heading>
            {{ $heading }}
        </x-filament::header.heading>

        @if ($subheading)
            <x-filament::header.subheading class="mt-2">
                {{ $subheading }}
            </x-filament::header.subheading>
        @endif
    </div>

    <x-filament-actions::actions
        :actions="$actions"
        @class([
            'shrink-0',
            'sm:mt-7' => $breadcrumbs,
        ])
    />
</header>
