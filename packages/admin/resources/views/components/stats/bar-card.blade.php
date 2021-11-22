@props([
    'columns' => [],
    'data' => [],
    'heading' => null,
])

<x-app-ui::card {{ $attributes }}>
    @if ($heading)
        <x-slot name="header">
            <x-app-ui::card.heading>
                {{ $heading }}
            </x-app-ui::card.heading>
        </x-slot>
    @endif

    <div class="-my-2 space-y-1">
        <div class="{{ generateClasses([
            'grid gap-4 text-sm font-medium text-gray-500 pb-2',
            'grid-cols-2' => count($columns) === 1,
            'grid-cols-3' => count($columns) === 2,
            'grid-cols-4' => count($columns) === 3,
            'grid-cols-5' => count($columns) === 4,
            'grid-cols-6' => count($columns) === 5,
        ]) }}">
            @foreach ($columns as $id => $label)
                <div class="{{ $loop->first ? 'col-span-2' : 'text-right' }}">{{ $label }}</div>
            @endforeach
        </div>

        @foreach ($data as $entry)
            <div class="relative py-2 -mx-2">
                @if ($width = ($entry['width'] ?? null))
                    <div aria-hidden="true" class="absolute inset-0 h-full rounded-lg bg-primary-50" style="width: {{ $width }}%"></div>
                @endif

                <div class="{{ generateClasses([
                    'grid gap-4 text-sm font-medium px-2 relative',
                    'grid-cols-2' => count($columns) === 1,
                    'grid-cols-3' => count($columns) === 2,
                    'grid-cols-4' => count($columns) === 3,
                    'grid-cols-5' => count($columns) === 4,
                    'grid-cols-6' => count($columns) === 5,
                ]) }}">
                    @foreach (array_keys($columns) as $column)
                        <div class="{{ $loop->first ? 'col-span-2' : 'text-right' }}">{{ $entry[$column] ?? null }}</div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-app-ui::card>