@php
    use Filament\Support\Enums\Alignment;
@endphp

@props([
    'actions' => [],
    'description' => null,
    'heading',
    'icon',
])

<div
    {{ $attributes->class(['fi-ta-empty-state px-6 py-12']) }}
>
    <div
        class="fi-ta-empty-state-content mx-auto grid max-w-lg justify-items-center text-center"
    >
        <div
            class="fi-ta-empty-state-icon-ctn mb-4 rounded-full bg-gray-100 p-3 dark:bg-gray-500/20"
        >
            <x-filament::icon
                :icon="$icon"
                class="fi-ta-empty-state-icon h-6 w-6 text-gray-500 dark:text-gray-400"
            />
        </div>

        <x-filament-tables::empty-state.heading>
            {{ $heading }}
        </x-filament-tables::empty-state.heading>

        @if ($description)
            <x-filament-tables::empty-state.description class="mt-1">
                {{ $description }}
            </x-filament-tables::empty-state.description>
        @endif

        @if ($actions)
            <x-filament-tables::actions
                :actions="$actions"
                :alignment="Alignment::Center"
                wrap
                class="mt-6"
            />
        @endif
    </div>
</div>
