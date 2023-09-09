@props([
    'allSelectableRecordsCount',
    'deselectAllRecordsAction' => 'deselectAllRecords',
    'end' => null,
    'selectAllRecordsAction' => 'selectAllRecords',
    'selectedRecordsCount',
    'selectedRecordsPropertyName' => 'selectedRecords',
])

<div
    x-cloak
    {{
        $attributes
            ->merge([
                'wire:key' => "{$this->getId()}.table.selection.indicator",
            ], escape: false)
            ->class([
                'fi-ta-selection-indicator flex flex-col justify-between gap-y-1 bg-gray-50 px-3 py-2 dark:bg-white/5 sm:flex-row sm:items-center sm:px-6 sm:py-1.5',
            ])
    }}
>
    <div class="flex gap-x-3">
        <x-filament::loading-indicator
            x-show="isLoading"
            class="h-5 w-5 text-gray-400 dark:text-gray-500"
        />

        <span
            x-text="
                window.pluralize(@js(__('filament-tables::table.selection_indicator.selected_count')), {{ $selectedRecordsPropertyName }}.length, {
                    count: {{ $selectedRecordsPropertyName }}.length,
                })
            "
            class="text-sm font-medium leading-6 text-gray-700 dark:text-gray-200"
        ></span>
    </div>

    <div class="flex gap-x-3">
        <x-filament::link
            color="primary"
            :id="$this->getId() . '.table.selection.indicator.record-count.' . $allSelectableRecordsCount"
            tag="button"
            :x-on:click="$selectAllRecordsAction"
            :x-show="$allSelectableRecordsCount . ' !== ' . $selectedRecordsPropertyName . '.length'"
        >
            {{ trans_choice('filament-tables::table.selection_indicator.actions.select_all.label', $allSelectableRecordsCount) }}
        </x-filament::link>

        <x-filament::link
            color="danger"
            tag="button"
            :x-on:click="$deselectAllRecordsAction"
        >
            {{ __('filament-tables::table.selection_indicator.actions.deselect_all.label') }}
        </x-filament::link>

        {{ $end }}
    </div>
</div>
