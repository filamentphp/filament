<div {{ $attributes->class(['filament-global-search-input']) }}>
    <label for="globalSearchInput" class="sr-only">
        {{ __('filament::global-search.field.label') }}
    </label>

    <div class="relative group max-w-md">
        <span class="absolute inset-y-0 left-0 flex items-center justify-center w-10 h-10 pointer-events-none">
            <x-filament::icon
                name="heroicon-m-magnifying-glass"
                alias="app::global-search.input.prefix"
                color="text-gray-500 dark:text-gray-400"
                size="h-5 w-5"
                wire:loading.remove.delay=""
                wire:target="search"
            />

            <x-filament::loading-indicator
                class="h-5 w-5 text-gray-500 dark:text-gray-400"
                wire:loading.delay=""
                wire:target="search"
            />
        </span>

        <input
            wire:model.debounce.500ms="search"
            id="globalSearchInput"
            placeholder="{{ __('filament::global-search.field.placeholder') }}"
            type="search"
            autocomplete="off"
            class="block w-full h-10 pl-10 bg-gray-400/10 placeholder-gray-500 border-transparent transition duration-75 rounded-lg outline-none focus:bg-white focus:placeholder-gray-400 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 dark:bg-gray-700 dark:text-gray-200 dark:placeholder-gray-400"
        >
    </div>
</div>
