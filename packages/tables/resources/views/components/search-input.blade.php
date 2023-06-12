@props([
    'wireModel' => 'tableSearchQuery',
])

<div {{ $attributes->class(['filament-tables-search-input']) }}>
    <label class="group relative flex items-center">
        <span
            class="pointer-events-none absolute inset-y-0 left-0 flex h-9 w-9 items-center justify-center text-gray-400 group-focus-within:text-primary-500"
        >
            <x-heroicon-o-search class="h-5 w-5" />
        </span>

        <input
            wire:model.debounce.500ms="{{ $wireModel }}"
            placeholder="{{ __('tables::table.fields.search_query.placeholder') }}"
            type="search"
            autocomplete="off"
            @class([
                'block h-9 w-full max-w-xs rounded-lg border-gray-300 pl-9 placeholder-gray-400 shadow-sm outline-none transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500',
                'dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400' => config('tables.dark_mode'),
            ])
        />

        <span class="sr-only">
            {{ __('tables::table.fields.search_query.label') }}
        </span>
    </label>
</div>
