@props([
    'allSelectableRecordsCount',
    'colspan',
    'selectedRecordsCount',
])

<div
    wire:key="{{ $this->id }}.table.selection.indicator"
    x-cloak
    {{ $attributes->class(['filament-tables-selection-indicator whitespace-nowrap bg-primary-500/10 px-4 py-2 text-sm']) }}
>
    <x-filament-support::loading-indicator
        x-show="isLoading"
        class="mr-3 inline-block h-4 w-4 text-primary-500 rtl:ml-3 rtl:mr-0"
    />

    <span
        @class(['dark:text-white' => config('tables.dark_mode')])
        x-text="window.pluralize(@js(__('tables::table.selection_indicator.selected_count')), selectedRecords.length, { count: selectedRecords.length })"
    ></span>

    <span
        id="{{ $this->id }}.table.selection.indicator.record-count.{{ $allSelectableRecordsCount }}"
        x-show="{{ $allSelectableRecordsCount }} !== selectedRecords.length"
    >
        <button
            x-on:click="selectAllRecords"
            class="text-sm font-medium text-primary-600"
            type="button"
        >
            {{ trans_choice('tables::table.selection_indicator.buttons.select_all.label', $allSelectableRecordsCount) }}.
        </button>
    </span>

    <span>
        <button
            x-on:click="deselectAllRecords"
            class="text-sm font-medium text-primary-600"
            type="button"
        >
            {{ __('tables::table.selection_indicator.buttons.deselect_all.label') }}.
        </button>
    </span>
</div>
