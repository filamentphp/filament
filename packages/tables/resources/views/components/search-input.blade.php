<div {{ $attributes->class(['filament-tables-search-input']) }}>
    <label for="tableSearchQueryInput" class="sr-only">
        {{ __('tables::table.fields.search_query.label') }}
    </label>

    <div class="relative group">
        <span class="absolute inset-y-0 left-0 flex items-center justify-center w-9 h-9 text-gray-400 pointer-events-none group-focus-within:text-primary-500">
            <x-heroicon-o-search class="w-5 h-5" />
        </span>

        <input
            wire:model.debounce.1s="tableSearchQuery"
            id="tableSearchQueryInput"
            placeholder="{{ __('tables::table.fields.search_query.placeholder') }}"
            type="search"
            autocomplete="off"
            @class([
                'block w-full h-9 pl-9 placeholder-gray-400 transition duration-75 border-gray-200 rounded-lg shadow-sm focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600',
                'dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400' => config('tables.dark_mode'),
            ])
        >
    </div>
</div>
