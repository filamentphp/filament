@props([
    'actions' => null,
    'description' => null,
    'heading',
    'icon',
])

<div
    {{ $attributes->class(['filament-tables-empty-state mx-auto flex flex-1 flex-col items-center justify-center space-y-6 bg-white p-6 text-center dark:bg-gray-800']) }}
>
    <div
        class="filament-tables-empty-state-icon-wrapper flex h-16 w-16 items-center justify-center rounded-full bg-primary-50 text-primary-500 dark:bg-gray-700"
    >
        <x-filament::icon
            :name="$icon"
            alias="tables::empty-state"
            size="h-6 w-6"
            wire:loading.remove.delay=""
            :wire:target="implode(',', \Filament\Tables\Table::LOADING_TARGETS)"
        />

        <x-filament::loading-indicator
            class="h-6 w-6"
            wire:loading.delay=""
            wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
        />
    </div>

    <div
        class="filament-tables-empty-state-textual-content-wrapper max-w-md space-y-1"
    >
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
