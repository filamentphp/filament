@props([
    'allRecordsCount',
    'areAllRecordsOnCurrentPageSelected' => false,
    'colspan',
    'selectedRecordsCount',
])

<tr {{ $attributes->class(['bg-primary-500/10']) }}>
    <td class="px-4 py-2 whitespace-nowrap text-sm" colspan="{{ $colspan }}">
        <div>
            <span>
                {{ trans_choice('tables::table.selection_indicator.selected_count', $selectedRecordsCount, ['count' => $selectedRecordsCount]) }}
            </span>

            @if ($allRecordsCount !== $selectedRecordsCount)
                <span>
                    <button wire:click="toggleSelectAllTableRecords" class="text-primary-600 text-sm font-medium">
                        {{ __('tables::table.selection_indicator.buttons.select_all.label', ['count' => $allRecordsCount]) }}.
                    </button>
                </span>
            @endif

            <span>
                <button wire:click="deselectAllTableRecords" class="text-primary-600 text-sm font-medium">
                    {{ __('tables::table.selection_indicator.buttons.deselect_all.label') }}.
                </button>
            </span>
        </div>
    </td>
</tr>
