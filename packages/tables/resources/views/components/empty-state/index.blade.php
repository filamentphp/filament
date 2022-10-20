@props([
    'actions' => null,
    'description' => null,
    'heading',
    'icon',
])

<div {{ $attributes->class([
    'filament-tables-empty-state flex flex-1 flex-col items-center justify-center p-6 mx-auto space-y-6 text-center bg-white',
    'dark:bg-gray-800' => config('filament-tables.dark_mode'),
]) }}>
    <div @class([
        'flex items-center justify-center w-16 h-16 text-primary-500 rounded-full bg-primary-50',
        'dark:bg-gray-700' => config('filament-tables.dark_mode'),
    ])>
        <x-filament-support::icon
            :name="$icon"
            alias="filament-tables::empty-state"
            size="h-6 w-6"
            wire:loading.remove.delay
            :wire:target="implode(',', \Filament\Tables\Table::LOADING_TARGETS)"
        />

        <x-filament-support::loading-indicator
            class="h-6 w-6"
            wire:loading.delay
            wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
        />
    </div>

    <div class="max-w-md space-y-1">
        <x-filament-tables::empty-state.heading>
            {{ $heading }}
        </x-filament-tables::empty-state.heading>

        @if ($description)
            <x-filament-tables::empty-state.description>
                {{ $description }}
            </x-filament-tables::empty-state.description>
        @endif
    </div>

    @if ($actions)
        <x-filament-tables::actions
            :actions="$actions"
            alignment="center"
            wrap
        />
    @endif
</div>
