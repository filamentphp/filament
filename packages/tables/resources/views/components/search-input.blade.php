@props([
    'wireModel' => 'tableSearch',
])

<div {{ $attributes->class(['filament-tables-search-input']) }}>
    <label class="group relative flex items-center">
        <span
            class="filament-tables-search-input-icon-wrapper pointer-events-none absolute inset-y-0 start-0 flex h-9 w-9 items-center justify-center"
        >
            <x-filament::icon
                name="heroicon-m-magnifying-glass"
                alias="tables::search-input.prefix"
                color="text-gray-400"
                size="h-5 w-5"
            />
        </span>

        <input
            wire:model.live.debounce.500ms="{{ $wireModel }}"
            placeholder="{{ __('filament-tables::table.fields.search.placeholder') }}"
            type="text"
            autocomplete="off"
            class="block h-9 w-full min-w-[8rem] max-w-xs rounded-lg border-gray-300 ps-9 placeholder-gray-400 shadow-sm outline-none transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 sm:text-sm"
        />

        <span class="sr-only">
            {{ __('filament-tables::table.fields.search.label') }}
        </span>
    </label>
</div>
