@props([
    'actions' => [],
    'actionsPosition',
    'description' => null,
    'heading' => null,
])

<div
    {{
        $attributes->class([
            'fi-ta-header flex flex-col gap-4 px-3 py-3 sm:px-6 sm:py-4',
            'sm:flex-row sm:items-center sm:justify-between' => $actionsPosition === \Filament\Tables\Actions\HeaderActionsPosition::Adaptive,
        ])
    }}
>
    @if ($heading || $description)
        <div class="grid gap-y-2">
            @if ($heading)
                <h3
                    class="fi-ta-header-heading text-base font-semibold leading-6"
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
            alignment="start"
            wrap
            class="ms-auto"
        />
    @endif
</div>
