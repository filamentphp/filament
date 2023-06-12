@props([
    'actions' => null,
    'columnSearches' => false,
    'description' => null,
    'heading',
    'icon',
])

<div
    {{
        $attributes->class([
            'filament-tables-empty-state mx-auto flex flex-1 flex-col items-center justify-center space-y-6 bg-white p-6 text-center',
            'dark:bg-gray-800' => config('tables.dark_mode'),
        ])
    }}
>
    <div
        @class([
            'flex h-16 w-16 items-center justify-center rounded-full bg-primary-50 text-primary-500',
            'dark:bg-gray-700' => config('tables.dark_mode'),
        ])
    >
        <x-dynamic-component
            :component="$icon"
            class="h-6 w-6"
            wire:loading.remove.delay
            wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
        />

        <x-filament-support::loading-indicator
            class="h-6 w-6"
            wire:loading.delay
            wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
        />
    </div>

    <div class="max-w-md space-y-1">
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
        <x-tables::actions :actions="$actions" alignment="center" wrap />
    @endif

    @if ($columnSearches)
        <x-tables::link
            wire:click="$set('tableColumnSearchQueries', [])"
            color="danger"
            tag="button"
            size="sm"
        >
            {{ __('tables::table.empty.buttons.reset_column_searches.label') }}
        </x-tables::link>
    @endif
</div>
