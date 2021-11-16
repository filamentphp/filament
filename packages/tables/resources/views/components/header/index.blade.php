@props([
    'actions' => [],
    'description' => null,
    'heading',
])

<div {{ $attributes->class(['px-4 py-2']) }}>
    <div class="space-y-2 items-center justify-between md:flex md:space-y-0 md:space-x-2">
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

        @if (count($actions))
            <x-tables::actions :actions="$actions" class="-mr-2" />
        @endif
    </div>
</div>
