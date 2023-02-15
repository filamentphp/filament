@props([
    'indicators' => [],
])

@if (count($indicators))
    <div {{ $attributes->class(['filament-tables-filter-indicators bg-gray-500/5 gap-x-4 px-4 py-1 text-sm flex']) }}>
        <div class="flex flex-1 items-center flex-wrap gap-y-1 gap-x-2">
            <span class="font-medium dark:text-gray-200">
                {{ __('filament-tables::table.filters.indicator') }}
            </span>

            @foreach ($indicators as $filter => $filterIndicators)
                @foreach ($filterIndicators as $field => $indicator)
                    @php
                        $field = is_numeric($field) ? null : $field;
                    @endphp

                    <span class="filament-tables-filter-indicator inline-flex items-center justify-center min-h-6 px-2 py-0.5 text-xs font-medium tracking-tight rounded-xl text-gray-700 bg-gray-500/10 whitespace-normal dark:text-gray-300 dark:bg-gray-500/20">
                        {{ $indicator }}

                        <button
                            wire:click="removeTableFilter('{{ $filter }}'{{ $field ? ' , \'' . $field . '\'' : null }})"
                            wire:loading.attr="disabled"
                            wire:target="removeTableFilter"
                            type="button"
                            class="ml-1 -mr-2 rtl:mr-1 rtl:-ml-2 p-1 -my-1 hover:bg-gray-500/10 rounded-full"
                        >
                            <x-filament::icon
                                name="heroicon-m-x-mark"
                                alias="filament-tables::filters.buttons.remove"
                                size="h-3 w-3"
                            />

                            <span class="sr-only">
                                {{ __('filament-tables::table.filters.buttons.remove.label') }}
                            </span>
                        </button>
                    </span>
                @endforeach
            @endforeach
        </div>

        <div class="shrink-0">
            <button
                wire:click="removeTableFilters"
                type="button"
                class="-mb-1.5 -mt-0.5 -mr-2 p-1.5 text-gray-600 hover:bg-gray-500/10 rounded-full hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-500/20"
            >
                <div class="w-5 h-5 flex items-center justify-center">
                    <x-filament::icon
                        name="heroicon-m-x-mark"
                        alias="filament-tables::filters.buttons.remove-all"
                        size="h-5 w-5"
                        :x-tooltip.raw="__('filament-tables::table.filters.buttons.remove_all.tooltip')"
                        wire:loading.remove.delay=""
                        wire:target="removeTableFilters,removeTableFilter"
                    />

                    <x-filament::loading-indicator
                        wire:loading.delay=""
                        wire:target="removeTableFilters,removeTableFilter"
                        class="w-5 h-5"
                    />
                </div>

                <span class="sr-only">
                    {{ __('filament-tables::table.filters.buttons.remove_all.label') }}
                </span>
            </button>
        </div>
    </div>
@endif
