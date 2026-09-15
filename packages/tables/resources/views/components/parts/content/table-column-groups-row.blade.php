@php
    use Filament\Support\Enums\Alignment;
    use Filament\Tables\Columns\Column;
    use Filament\Tables\Columns\ColumnGroup;
    use Filament\Tables\Enums\RecordActionsPosition;
    use Filament\Tables\Enums\RecordCheckboxPosition;
@endphp

@if ($hasColumnGroups)
    <tr class="fi-ta-table-head-groups-row">
        @if (count($records))
            @if ($isReordering)
                <th></th>
            @else
                @if ($hasRecordActionsForAnyRecord && in_array($recordActionsPosition, [RecordActionsPosition::BeforeCells, RecordActionsPosition::BeforeColumns]))
                    <th></th>
                @endif

                @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
                    <th></th>
                @endif
            @endif
        @endif

        @foreach ($columnsLayout as $columnGroup)
            @if ($columnGroup instanceof Column)
                @if ($columnGroup->isVisible() && (! $columnGroup->isToggledHidden()))
                    <th></th>
                @endif
            @elseif ($columnGroup instanceof ColumnGroup)
                @php
                    $columnGroupColumnsCount = count($columnGroup->getVisibleColumns());
                @endphp

                @if ($columnGroupColumnsCount)
                    <th
                        colspan="{{ $columnGroupColumnsCount }}"
                        scope="colgroup"
                        {{
                            $columnGroup->getExtraHeaderAttributeBag()->class([
                                'fi-ta-header-group-cell',
                                'fi-wrapped' => $columnGroup->canHeaderWrap(),
                                ((($columnGroupAlignment = $columnGroup->getAlignment()) instanceof Alignment) ? "fi-align-{$columnGroupAlignment->value}" : (is_string($columnGroupAlignment) ? $columnGroupAlignment : '')),
                                (filled($columnGroupHiddenFrom = $columnGroup->getHiddenFrom()) ? "{$columnGroupHiddenFrom}:fi-hidden" : ''),
                                (filled($columnGroupVisibleFrom = $columnGroup->getVisibleFrom()) ? "{$columnGroupVisibleFrom}:fi-visible" : ''),
                            ])
                        }}
                    >
                        {{ $columnGroup->getLabel() }}
                    </th>
                @endif
            @endif
        @endforeach

        @if ((! $isReordering) && count($records))
            @if ($hasRecordActionsForAnyRecord && in_array($recordActionsPosition, [RecordActionsPosition::AfterColumns, RecordActionsPosition::AfterCells]))
                <th></th>
            @endif

            @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
                <th></th>
            @endif
        @endif
    </tr>
@endif
