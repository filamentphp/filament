@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\IconSize;
    use Filament\Tables\Actions\HeaderActionsPosition;
@endphp

@props([
    'actions' => [],
    'actionsPosition',
    'description' => null,
    'heading' => null,
    'icon' => null,
    'iconColor' => 'gray',
    'iconSize' => IconSize::Large,
])

<div
    {{
        $attributes->class([
            'fi-ta-header flex flex-col gap-3 p-4 sm:px-6',
            'sm:flex-row sm:items-center' => $actionsPosition === HeaderActionsPosition::Adaptive,
        ])
    }}
>
    @if ($icon)
        <x-filament::icon
            :icon="$icon"
            @class([
                'fi-ta-header-icon self-start',
                match ($iconColor) {
                    'gray' => 'text-gray-400 dark:text-gray-500',
                    default => 'fi-color-custom text-custom-500 dark:text-custom-400',
                },
                is_string($iconColor) ? "fi-color-{$iconColor}" : null,
                match ($iconSize) {
                    IconSize::Small, 'sm' => 'h-4 w-4 mt-1',
                    IconSize::Medium, 'md' => 'h-5 w-5 mt-0.5',
                    IconSize::Large, 'lg' => 'h-6 w-6',
                    default => $iconSize,
                },
            ])
            @style([
                \Filament\Support\get_color_css_variables(
                    $iconColor,
                    shades: [400, 500],
                    alias: 'table.header.icon',
                ) => $iconColor !== 'gray',
            ])
        />
    @endif

    @if ($heading || $description)
        <div class="grid gap-y-1">
            @if ($heading)
                <h3
                    class="fi-ta-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white"
                >
                    {{ $heading }}
                </h3>
            @endif

            @if ($description)
                <p
                    class="fi-ta-header-description text-sm text-gray-600 dark:text-gray-400"
                >
                    {{ $description }}
                </p>
            @endif
        </div>
    @endif

    @if ($actions)
        <x-filament-tables::actions
            :actions="$actions"
            :alignment="Alignment::Start"
            wrap
            @class([
                'ms-auto' => $actionsPosition === HeaderActionsPosition::Adaptive &&
                    ! ($heading || $description),
                'sm:ms-auto' => $actionsPosition === HeaderActionsPosition::Adaptive,
            ])
        />
    @endif
</div>
