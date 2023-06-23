@php
    use Filament\Tables\Actions\Position as ActionsPosition;
@endphp

@props([
    'actions' => [],
    'actionsPosition' => ActionsPosition::End,
    'description' => null,
    'heading' => null,
])

<div {{ $attributes->class(['filament-tables-header px-4 py-2']) }}>
    <div class="flex flex-col gap-4 md:flex-row md:items-center">
        @if ($heading || $description || $actionsPosition === ActionsPosition::Start)
            <div>
                @if ($heading)
                    <h3
                        class="filament-tables-header-heading text-base font-semibold leading-6"
                    >
                        {{ $heading }}
                    </h3>
                @endif

                @if ($description)
                    <p
                        class="filament-tables-header-description mt-1 text-sm text-gray-500 dark:text-gray-400"
                    >
                        {{ $description }}
                    </p>
                @endif

                @if ($actionsPosition === ActionsPosition::Start)
                    <x-filament-tables::actions
                        :actions="$actions"
                        alignment="start"
                        wrap
                        @class([
                            'md:-ms-2' => ! ($heading || $description),
                            'mt-2' => $heading || $description,
                        ])
                    />
                @endif
            </div>
        @endif

        @if ($actionsPosition === ActionsPosition::End)
            <x-filament-tables::actions
                :actions="$actions"
                alignment="end"
                wrap
                class="ms-auto shrink-0 md:-me-2"
            />
        @endif
    </div>
</div>
