@props([
    'allRecordsCount',
    'colspan',
    'selectedRecordsCount',
])

<div
    wire:key="{{ $this->id }}.table.selection.indicator"
    x-cloak
    {{ $attributes->class(['filament-tables-selection-indicator bg-primary-500/10 px-4 py-2 whitespace-nowrap text-sm']) }}
>
    <x-filament-support::loading-indicator
        x-show="isLoading"
        class="inline-block animate-spin w-4 h-4 mr-3 text-primary-500"
    />

    <span @class(['dark:text-white' => config('tables.dark_mode')]) x-text="pluralize(@js(__('tables::table.selection_indicator.selected_count')), selectedRecords.length, { count: selectedRecords.length })"></span>

    <span x-show="{{ $allRecordsCount }} !== selectedRecords.length">
        <button x-on:click="selectAllRecords" class="text-primary-600 text-sm font-medium">
            {{ __('tables::table.selection_indicator.buttons.select_all.label', ['count' => $allRecordsCount]) }}.
        </button>
    </span>

    <span>
        <button x-on:click="deselectAllRecords" class="text-primary-600 text-sm font-medium">
            {{ __('tables::table.selection_indicator.buttons.deselect_all.label') }}.
        </button>
    </span>
</div>
