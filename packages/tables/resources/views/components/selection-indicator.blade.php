@props([
    'selectedRecordCount',
    'allRecordsCount',
    'getColumnsCount',
    'areAllRecordsOnCurrentPageSelected' => false,
    'areAllRecordsSelected' => false,
])

<tr class="bg-primary-500/10">
    <td class="px-4 py-2 whitespace-nowrap text-sm" colspan="{{ $getColumnsCount }}">
        <div>
            <span>
                {{ trans_choice('tables::table.indicator.selected_rows', $selectedRecordCount, ['count' => $selectedRecordCount]) }}

                 @if($areAllRecordsOnCurrentPageSelected && !$areAllRecordsSelected)
                    <button wire:click="toggleSelectAllTableRecords" class="text-primary-600 text-sm font-medium">
                        {{ __('tables::table.indicator.buttons.select_all.label') }}
                    </button>

                    <span>
                        {{ __('tables::table.indicator.all_records', ['count' => $allRecordsCount]) }}
                    </span>
                @endif

                <button wire:click="deselectAllTableRecords" class="text-primary-600 text-sm font-medium">
                    {{ __('tables::table.indicator.buttons.deselect_all.label') }}
                </button>
            </span>
        </div>
    </td>
</tr>
