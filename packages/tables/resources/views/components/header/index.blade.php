@props([
    'actions' => [],
    'description' => null,
    'heading',
])

<div {{ $attributes->class(['filament-tables-header px-4 py-2']) }}>
    <div
        class="flex flex-col gap-4 md:-mr-2 md:flex-row md:items-start md:justify-between"
    >
        <div>
            @if ($heading)
                <x-tables::header.heading>
                    {{ $heading }}
                </x-tables::header.heading>
            @endif

            @if ($description)
                <x-tables::header.description>
                    {{ $description }}
                </x-tables::header.description>
            @endif
        </div>

        <x-tables::actions
            :actions="$actions"
            alignment="right"
            wrap
            class="shrink-0"
        />
    </div>
</div>
