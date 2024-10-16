@php
    use Filament\Support\Enums\Alignment;
@endphp

@props([
    'actions' => [],
    'description' => null,
    'heading',
    'icon',
    'iconColor',
])

<div
    {{ $attributes->class(['mx-auto my-auto grid max-w-lg justify-items-center p-6 text-center']) }}
>
    <div
        @class([
            'mb-4 rounded-full p-3',
            match ($iconColor) {
                'gray' => 'bg-gray-100 dark:bg-gray-500/20',
                default => 'bg-custom-100 dark:bg-custom-500/20',
            },
        ])
        @style([
            \Filament\Support\get_color_css_variables(
                $iconColor,
                shades: [100, 400, 500, 600],
            ) => $iconColor !== 'gray',
        ])
    >
        <x-filament::icon
            :icon="$icon"
            @class([
                'h-6 w-6',
                match ($iconColor) {
                    'gray' => 'text-gray-500 dark:text-gray-400',
                    default => 'text-custom-600 dark:text-custom-400',
                },
            ])
        />
    </div>

    <x-filament::empty-state.heading>
        {{ $heading }}
    </x-filament::empty-state.heading>

    @if ($description)
        <x-filament::empty-state.description class="mt-1">
            {{ $description }}
        </x-filament::empty-state.description>
    @endif

    @if ($actions)
        <x-filament::empty-state.actions :actions="$actions" />
    @endif
</div>
