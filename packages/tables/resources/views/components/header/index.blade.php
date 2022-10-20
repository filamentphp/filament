@props([
    'actions' => [],
    'description' => null,
    'heading',
])

<div {{ $attributes->class(['filament-tables-header px-4 py-2']) }}>
    <div class="flex flex-col gap-4 md:justify-between md:items-start md:flex-row md:-mr-2">
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
        </div>

        <x-filament-tables::actions
            :actions="$actions"
            alignment="right"
            wrap
            class="shrink-0"
        />
    </div>
</div>
