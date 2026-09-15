@php
    use Filament\Tables\Enums\RecordActionsPosition;
    use Filament\Tables\Enums\RecordCheckboxPosition;
@endphp

@if ($isColumnSearchVisible)
    <tr class="fi-ta-row fi-ta-row-not-reorderable">
        @if (count($records))
            @if ($isReordering)
                <td></td>
            @else
                @if ($hasRecordActionsForAnyRecord && in_array($recordActionsPosition, [RecordActionsPosition::BeforeCells, RecordActionsPosition::BeforeColumns]))
                    <td></td>
                @endif

                @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
                    <td></td>
                @endif
            @endif
        @endif

        @foreach ($columns as $column)
            @php
                $columnName = $column->getName();
            @endphp

            <td
                @class([
                    'fi-ta-cell',
                    'fi-ta-individual-search-cell' => $isIndividuallySearchable = $column->isIndividuallySearchable(),
                    'fi-ta-individual-search-cell-' . str($columnName)->camel()->kebab() => $isIndividuallySearchable,
                ])
            >
                @if ($isIndividuallySearchable)
                    <x-filament-tables::search-field
                        :debounce="$searchDebounce"
                        :on-blur="$isSearchOnBlur"
                        :wire-model="'tableColumnSearches.' . $columnName"
                    />
                @endif
            </td>
        @endforeach

        @if ((! $isReordering) && count($records))
            @if ($hasRecordActionsForAnyRecord && in_array($recordActionsPosition, [RecordActionsPosition::AfterColumns, RecordActionsPosition::AfterCells]))
                <td></td>
            @endif

            @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
                <td></td>
            @endif
        @endif
    </tr>
@endif
