@props([
    'actions' => null,
    'description' => null,
    'heading',
    'icon',
])

<div {{ $attributes->class([
    'flex flex-1 flex-col items-center justify-center p-6 mx-auto space-y-6 text-center bg-white filament-tables-empty-state',
    'dark:bg-gray-800' => config('tables.dark_mode'),
]) }}>
    <div @class([
        'flex items-center justify-center w-16 h-16 text-primary-500 rounded-full bg-primary-50',
        'dark:bg-gray-700' => config('tables.dark_mode'),
    ])>
        <x-dynamic-component :component="$icon" class="w-6 h-6" />
    </div>

    <div class="max-w-xs space-y-1">
        <x-tables::empty-state.heading>
            {{ $heading }}
        </x-tables::empty-state.heading>

        @if ($description)
            <x-tables::empty-state.description>
                {{ $description }}
            </x-tables::empty-state.description>
        @endif
    </div>

    @if ($actions)
        <x-tables::actions :actions="$actions" class="justify-center" />
    @endif
</div>
