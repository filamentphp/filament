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
    x-cloak
    {{
        $attributes
            ->merge([
                'wire:key' => "{$this->getId()}.table.selection.indicator",
            ], escape: false)
            ->class(['fi-ta-selection-indicator flex flex-wrap items-center gap-1 whitespace-nowrap bg-primary-500/10 px-4 py-2 text-sm'])
    }}
>
    {{ $slot }}

    <div class="flex-1">
        <x-filament::loading-indicator
            x-show="isLoading"
            class="me-3 inline-block h-4 w-4 text-primary-500"
        />

        <span
            class="dark:text-white"
            x-text="
                window.pluralize(@js(__('filament-tables::table.selection_indicator.selected_count')), {{ $selectedRecordsPropertyName }}.length, {
                    count: {{ $selectedRecordsPropertyName }}.length,
                })
            "
        ></span>

        <span
            id="{{ $this->getId() }}.table.selection.indicator.record-count.{{ $allSelectableRecordsCount }}"
            x-show="{{ $allSelectableRecordsCount }} !== {{ $selectedRecordsPropertyName }}.length"
        >
            <button
                x-on:click="{{ $selectAllRecordsAction }}"
                class="text-sm font-medium text-primary-600"
                type="button"
            >
                {{ trans_choice('filament-tables::table.selection_indicator.actions.select_all.label', $allSelectableRecordsCount) }}.
            </button>
        </span>

        <span>
            <button
                x-on:click="{{ $deselectAllRecordsAction }}"
                class="text-sm font-medium text-primary-600"
                type="button"
            >
                {{ __('filament-tables::table.selection_indicator.actions.deselect_all.label') }}.
            </button>
        </span>
    </div>

    {{ $end }}
</div>
