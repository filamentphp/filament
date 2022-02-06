<div {{ $attributes->class(['filament-global-search-input']) }}>
    <label for="globalSearchQueryInput" class="sr-only">
        {{ __('filament::global-search.field.label') }}
    </label>

    <div class="relative group max-w-md">
        <span @class([
            'absolute inset-y-0 left-0 flex items-center justify-center w-10 h-10 text-gray-500 pointer-events-none group-focus-within:text-primary-500',
            'dark:text-gray-400' => config('filament.dark_mode'),
        ])>
            <x-heroicon-o-search class="w-5 h-5" />
        </span>

        <input
            wire:model.debounce.500ms="searchQuery"
            id="globalSearchQueryInput"
            placeholder="{{ __('filament::global-search.field.placeholder') }}"
            type="search"
            autocomplete="off"
            @class([
                'block w-full h-10 pl-10 lg:text-lg bg-gray-400/10 placeholder-gray-500 border-transparent transition duration-75 rounded-lg focus:bg-white focus:placeholder-gray-400 focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600',
                'dark:bg-gray-700 dark:text-gray-200 dark:placeholder-gray-400' => config('filament.dark_mode'),
            ])
        >
    </div>
</div>
