@props([
    'indicators' => [],
])

@if (count($indicators))
    <div
        {{ $attributes->class(['filament-tables-filter-indicators flex gap-x-4 bg-gray-500/5 px-4 py-1 text-sm']) }}
    >
        <div class="flex flex-1 flex-wrap items-center gap-x-2 gap-y-1">
            <span class="font-medium dark:text-gray-200">
                {{ __('filament-tables::table.filters.indicator') }}
            </span>

            @foreach ($indicators as $wireClickHandler => $label)
                <span
                    class="filament-tables-filter-indicator min-h-6 inline-flex items-center justify-center whitespace-normal rounded-xl bg-gray-500/10 px-2 py-0.5 text-xs font-medium tracking-tight text-gray-700 dark:bg-gray-500/20 dark:text-gray-300"
                >
                    {{ $label }}

                    <button
                        wire:click="{{ $wireClickHandler }}"
                        wire:loading.attr="disabled"
                        wire:target="removeTableFilter"
                        type="button"
                        class="-my-1 -me-2 ms-1 rounded-full p-1 hover:bg-gray-500/10"
                    >
                        <x-filament::icon
                            name="heroicon-m-x-mark"
                            alias="tables::filters.buttons.remove"
                            size="h-3 w-3"
                        />

                        <span class="sr-only">
                            {{ __('filament-tables::table.filters.buttons.remove.label') }}
                        </span>
                    </button>
                </span>
            @endforeach
        </div>

        <div class="shrink-0">
            <button
                wire:click="removeTableFilters"
                type="button"
                class="-mb-1.5 -me-2 -mt-0.5 rounded-full p-1.5 text-gray-600 hover:bg-gray-500/10 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-500/20 dark:hover:text-gray-300"
            >
                <div class="flex h-5 w-5 items-center justify-center">
                    <x-filament::icon
                        name="heroicon-m-x-mark"
                        alias="tables::filters.buttons.remove-all"
                        size="h-5 w-5"
                        :x-tooltip.raw="__('filament-tables::table.filters.buttons.remove_all.tooltip')"
                        wire:loading.remove.delay=""
                        wire:target="removeTableFilters,removeTableFilter"
                    />

                    <x-filament::loading-indicator
                        wire:loading.delay=""
                        wire:target="removeTableFilters,removeTableFilter"
                        class="h-5 w-5"
                    />
                </div>

                <span class="sr-only">
                    {{ __('filament-tables::table.filters.buttons.remove_all.label') }}
                </span>
            </button>
        </div>
    </div>
@endif
