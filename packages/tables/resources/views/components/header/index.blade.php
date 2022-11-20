@php
    use Filament\Tables\Actions\Position as ActionsPosition;
@endphp

@props([
    'actions' => [],
    'actionsPosition' => ActionsPosition::End,
    'description' => null,
    'heading',
])

<div {{ $attributes->class(['filament-tables-header px-4 py-2']) }}>
    <div class="flex flex-col gap-4 md:justify-between md:items-start md:flex-row">
        <div>
            @if ($heading)
                <x-filament-tables::header.heading>
                    {{ $heading }}
                </x-filament-tables::header.heading>
            @endif

            @if ($description)
                <x-filament-tables::header.description>
                    {{ $description }}
                </x-filament-tables::header.description>
            @endif

            @if ($actionsPosition === ActionsPosition::Start)
                <x-filament-tables::actions
                    :actions="$actions"
                    alignment="start"
                    wrap
                    @class([
                        'md:-ml-2' => ! ($heading || $description),
                        'mt-2' => $heading || $description,
                    ])
                />
            @endif
        </div>

        @if ($actionsPosition === ActionsPosition::End)
            <x-filament-tables::actions
                :actions="$actions"
                alignment="end"
                wrap
                class="shrink-0 md:-mr-2"
            />
        @endif
    </div>
</div>
