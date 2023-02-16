@props([
    'wireModel' => 'tableSearch',
])

<div {{ $attributes->class(['filament-tables-search-input']) }}>
    <label class="relative flex items-center group">
        <span class="absolute inset-y-0 left-0 flex items-center justify-center w-9 h-9 pointer-events-none">
            <x-filament::icon
                name="heroicon-m-magnifying-glass"
                alias="filament-tables::search-input.prefix"
                color="text-gray-400"
                size="h-5 w-5"
            />
        </span>

        <input
            wire:model.debounce.500ms="{{ $wireModel }}"
            placeholder="{{ __('filament-tables::table.fields.search.placeholder') }}"
            type="search"
            autocomplete="off"
            class="block w-full max-w-xs h-9 pl-9 placeholder-gray-400 transition duration-75 border-gray-300 rounded-lg shadow-sm outline-none sm:text-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
        />

        <span class="sr-only">
            {{ __('filament-tables::table.fields.search.label') }}
        </span>
    </label>
</div>
