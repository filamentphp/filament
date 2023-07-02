@props([
    'allSelectableRecordsCount',
    'colspan',
    'deselectAllRecordsAction' => 'deselectAllRecords',
    'end' => null,
    'selectAllRecordsAction' => 'selectAllRecords',
    'selectedRecordsCount',
    'selectedRecordsPropertyName' => 'selectedRecords',
])

<div
    wire:key="{{ $this->id }}.table.selection.indicator"
    x-cloak
    {{ $attributes->class(['filament-tables-selection-indicator flex flex-wrap items-center gap-1 whitespace-nowrap bg-primary-500/10 px-4 py-2 text-sm']) }}
>
    {{ $slot }}

    <div class="flex-1">
        <x-filament-support::loading-indicator
            x-show="isLoading"
            class="mr-3 inline-block h-4 w-4 text-primary-500 rtl:ml-3 rtl:mr-0"
        />

        <span
            @class(['dark:text-white' => config('tables.dark_mode')])
            x-text="
                window.pluralize(@js(__('tables::table.selection_indicator.selected_count')), {{ $selectedRecordsPropertyName }}.length, {
                    count: {{ $selectedRecordsPropertyName }}.length,
                })
            "
        ></span>

        <span
            id="{{ $this->id }}.table.selection.indicator.record-count.{{ $allSelectableRecordsCount }}"
            x-show="{{ $allSelectableRecordsCount }} !== {{ $selectedRecordsPropertyName }}.length"
        >
            <button
                x-on:click="{{ $selectAllRecordsAction }}"
                class="text-sm font-medium text-primary-600"
                type="button"
            >
                {{ trans_choice('tables::table.selection_indicator.buttons.select_all.label', $allSelectableRecordsCount) }}.
            </button>
        </span>

        <span>
            <button
                x-on:click="{{ $deselectAllRecordsAction }}"
                class="text-sm font-medium text-primary-600"
                type="button"
            >
                {{ __('tables::table.selection_indicator.buttons.deselect_all.label') }}.
            </button>
        </span>
    </div>

    {{ $end }}
</div>
