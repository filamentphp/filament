<div {{ $attributes->class(['filament-global-search-input']) }}>
    <label for="globalSearchInput" class="sr-only">
        {{ __('filament::global-search.field.label') }}
    </label>

    <div class="group relative max-w-md">
        <span
            class="pointer-events-none absolute inset-y-0 start-0 flex h-10 w-10 items-center justify-center"
        >
            <x-filament::icon
                name="heroicon-m-magnifying-glass"
                alias="panels::global-search.input.prefix"
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
            x-data="{}"
            wire:model.debounce.500ms="search"
            @if ($keyBindings = filament()->getGlobalSearchKeyBindings())
                x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}="$el.focus()"
            @endif
            id="globalSearchInput"
            placeholder="{{ __('filament::global-search.field.placeholder') }}"
            type="search"
            autocomplete="off"
            class="block h-10 w-full rounded-lg border-transparent bg-gray-400/10 ps-10 placeholder-gray-500 outline-none transition duration-75 focus:border-primary-500 focus:bg-white focus:placeholder-gray-400 focus:ring-1 focus:ring-inset focus:ring-primary-500 dark:bg-gray-700 dark:text-gray-200 dark:placeholder-gray-400"
        />
    </div>
</div>
