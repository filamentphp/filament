@php
    use Filament\Support\Enums\Alignment;
    use Filament\Tables\Actions\HeaderActionsPosition;
@endphp

@props([
    'actions' => [],
    'actionsPosition',
    'description' => null,
    'heading' => null,
])

<div
    {{
        $attributes->class([
            'fi-ta-header flex flex-col gap-3 p-4 sm:px-6',
            'sm:flex-row sm:items-center' => $actionsPosition === HeaderActionsPosition::Adaptive,
        ])
    }}
>
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
                'ms-auto' => $actionsPosition === HeaderActionsPosition::Adaptive && ! ($heading || $description),
                'sm:ms-auto' => $actionsPosition === HeaderActionsPosition::Adaptive,
            ])
        />
    @endif
</div>
