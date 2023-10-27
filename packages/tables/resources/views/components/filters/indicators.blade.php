@props([
    'indicators' => [],
])

<div
    {{ $attributes->class(['fi-ta-filter-indicators flex items-start justify-between gap-x-3 bg-gray-50 px-3 py-1.5 dark:bg-white/5 sm:px-6']) }}
>
    <div class="flex flex-col gap-x-3 gap-y-1 sm:flex-row">
        <span
            class="whitespace-nowrap text-sm font-medium leading-6 text-gray-700 dark:text-gray-200"
        >
            {{ __('filament-tables::table.filters.indicator') }}
        </span>

        <div class="flex flex-wrap gap-1.5">
            @foreach ($indicators as $wireClickHandler => $label)
                <x-filament::badge>
                    {{ $label }}

                    <x-slot
                        name="deleteButton"
                        :label="__('filament-tables::table.filters.actions.remove.label')"
                        wire:click="{{ $wireClickHandler }}"
                        wire:loading.attr="disabled"
                        wire:target="removeTableFilter"
                    ></x-slot>
                </x-filament::badge>
            @endforeach
        </div>
    </div>

    <div class="mt-0.5">
        <x-filament::icon-button
            color="gray"
            icon="heroicon-m-x-mark"
            icon-alias="tables::filters.remove-all-button"
            size="sm"
            :tooltip="__('filament-tables::table.filters.actions.remove_all.tooltip')"
            wire:click="removeTableFilters"
            wire:target="removeTableFilters,removeTableFilter"
        />
    </div>
</div>
