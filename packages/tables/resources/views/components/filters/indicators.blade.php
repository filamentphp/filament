@props([
    'indicators' => [],
])

@if (count($indicators))
    <div class="bg-gray-500/10 gap-x-4 px-4 py-2 text-sm flex">
        <div class="flex flex-1 items-center flex-wrap gap-y-2 gap-x-4">
            <span class="font-medium">
                {{ __('tables::table.filters.indicator') }}
            </span>

            @foreach ($indicators as $filter => $filterIndicators)
                @foreach ($filterIndicators as $field => $indicator)
                    @php
                        $field = is_numeric($field) ? null : $field;
                    @endphp

                    <span @class([
                        'inline-flex items-center justify-center min-h-6 mt-0.5 px-2 py-0.5 text-xs font-medium tracking-tight rounded-xl text-gray-700 bg-gray-500/10 whitespace-normal',
                        'dark:text-gray-500' => config('tables.dark_mode'),
                    ])>
                        {{ $indicator }}

                        <button
                            wire:click="resetTableFilterForm('{{ $filter }}'{{ $field ? ' , \'' . $field . '\'' : null }})"
                            type="button"
                            class="ml-1 -mr-2 p-1 -my-1 hover:bg-gray-500/10 rounded-full"
                        >
                            <x-heroicon-s-x class="w-3 h-3" />
                        </button>
                    </span>
                @endforeach
            @endforeach
        </div>

        <div class="flex-shrink-0">
            <button
                wire:click="resetTableFiltersForm"
                type="button"
                class="mt-1 text-gray-600 hover:text-gray-700"
            >
                <x-heroicon-s-x
                    :x-tooltip.raw="__('tables::table.filters.buttons.reset.label')"
                    wire:loading.remove
                    wire:target="resetTableFiltersForm"
                    class="w-5 h-5"
                />

                <x-filament-support::loading-indicator
                    wire:loading
                    wire:target="resetTableFiltersForm"
                    class="animate-spin w-5 h-5"
                />

                <span class="sr-only">
                    {{ __('tables::table.filters.buttons.reset.label') }}
                </span>
            </button>
        </div>
    </div>
@endif
