@props([
    'actions' => false,
    'actionsPosition' => null,
    'columns',
    'extraHeadingColumn' => false,
    'groupColumn' => null,
    'groupsOnly' => false,
    'heading',
    'placeholderColumns' => true,
    'query',
    'selectionEnabled' => false,
    'selectedState',
    'recordCheckboxPosition' => null,
])

@php
    use Filament\Support\Enums\Alignment;
    use Filament\Tables\Columns\Column;
    use Filament\Tables\Enums\RecordActionsPosition;
    use Filament\Tables\Enums\RecordCheckboxPosition;

    if ($groupsOnly && $groupColumn) {
        $columns = collect($columns)
            ->reject(fn (Column $column): bool => $column->getName() === $groupColumn)
            ->all();
    }

    // `$query` is constant for this render, so each column's resolved summarizers are
    // too. Resolve them once here instead of re-running `getSummarizers($query)` (and
    // `hasSummary($query)`, which wraps it) in every loop guard below. Keyed by the
    // `$columns` array key so the heading-span loop and the cell loop share the lookup.
    $columnsWithSummary = [];

    foreach ($columns as $summaryColumnKey => $summaryColumn) {
        $summaryColumnSummarizers = $summaryColumn->getSummarizers($query);

        $columnsWithSummary[$summaryColumnKey] = [
            'summarizers' => $summaryColumnSummarizers,
            'hasSummary' => (bool) count($summaryColumnSummarizers),
        ];
    }
@endphp

<tr {{ $attributes->class(['fi-ta-row fi-ta-summary-row']) }}>
    @if ($placeholderColumns && $actions && in_array($actionsPosition, [RecordActionsPosition::BeforeCells, RecordActionsPosition::BeforeColumns]))
        <td></td>
    @endif

    @if ($placeholderColumns && $selectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
        <td></td>
    @endif

    @if ($extraHeadingColumn || $groupsOnly)
        <th
            scope="row"
            class="fi-ta-cell fi-ta-summary-row-heading-cell fi-align-start"
        >
            {{ $heading }}
        </th>
    @else
        @php
            $headingColumnSpan = 1;

            foreach ($columns as $index => $column) {
                if ($index === array_key_first($columns)) {
                    continue;
                }

                if ($columnsWithSummary[$index]['hasSummary']) {
                    break;
                }

                $headingColumnSpan++;
            }
        @endphp
    @endif

    @foreach ($columns as $columnKey => $column)
        @if (($loop->first || $extraHeadingColumn || $groupsOnly || ($loop->iteration > $headingColumnSpan)) && ($placeholderColumns || $columnsWithSummary[$columnKey]['hasSummary']))
            @php
                $alignment = $column->getAlignment() ?? Alignment::Start;

                if (! $alignment instanceof Alignment) {
                    $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
                }

                // The leading cell labels the whole summary row, so render it as a row header; the aggregate
                // value cells stay `<td>` and gain a row association from this `<th scope="row">`.
                $isSummaryRowHeadingCell = $loop->first && (! $extraHeadingColumn) && (! $groupsOnly);
                $summaryCellTag = $isSummaryRowHeadingCell ? 'th' : 'td';
            @endphp

            <{{ $summaryCellTag }}
                @if ($isSummaryRowHeadingCell) scope="row" @endif
                @if ($isSummaryRowHeadingCell && ($headingColumnSpan > 1)) colspan="{{ $headingColumnSpan }}" @endif
                @class([
                    'fi-ta-cell',
                    ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : ''),
                    'fi-ta-summary-row-heading-cell' => $isSummaryRowHeadingCell,
                ])
            >
                @if ($isSummaryRowHeadingCell)
                    {{ $heading }}
                @elseif ((! $placeholderColumns) || $columnsWithSummary[$columnKey]['hasSummary'])
                    @foreach ($columnsWithSummary[$columnKey]['summarizers'] as $summarizer)
                        {{ $summarizer->query($query)->selectedState($selectedState) }}
                    @endforeach
                @endif
            </{{ $summaryCellTag }}>
        @endif
    @endforeach

    @if ($placeholderColumns && $actions && in_array($actionsPosition, [RecordActionsPosition::AfterColumns, RecordActionsPosition::AfterCells]))
        <td></td>
    @endif

    @if ($placeholderColumns && $selectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
        <td></td>
    @endif
</tr>
