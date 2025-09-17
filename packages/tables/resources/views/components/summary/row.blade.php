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
@endphp

<tr {{ $attributes->class(['fi-ta-row fi-ta-summary-row']) }}>
    @if ($placeholderColumns && $actions && in_array($actionsPosition, [RecordActionsPosition::BeforeCells, RecordActionsPosition::BeforeColumns]))
        <td></td>
    @endif

    @if ($placeholderColumns && $selectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
        <td></td>
    @endif

    @if ($extraHeadingColumn || $groupsOnly)
        <td class="fi-ta-cell fi-ta-summary-row-heading-cell">
            {{ $heading }}
        </td>
    @else
        @php
            $headingColumnSpan = 1;

            foreach ($columns as $index => $column) {
                if ($index === array_key_first($columns)) {
                    continue;
                }

                if ($column->hasSummary($query)) {
                    break;
                }

                $headingColumnSpan++;
            }
        @endphp
    @endif

    @foreach ($columns as $column)
        @if (($loop->first || $extraHeadingColumn || $groupsOnly || ($loop->iteration > $headingColumnSpan)) && ($placeholderColumns || $column->hasSummary($query)))
            @php
                $alignment = $column->getAlignment() ?? Alignment::Start;

                if (! $alignment instanceof Alignment) {
                    $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
                }
            @endphp

            <td
                colspan="{{ ($loop->first && (! $extraHeadingColumn) && (! $groupsOnly) && ($headingColumnSpan > 1)) ? $headingColumnSpan : null }}"
                @class([
                    'fi-ta-cell',
                    ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : ''),
                    'fi-ta-summary-row-heading-cell' => $loop->first && (! $extraHeadingColumn) && (! $groupsOnly),
                ])
            >
                @if ($loop->first && (! $extraHeadingColumn) && (! $groupsOnly))
                    {{ $heading }}
                @elseif ((! $placeholderColumns) || $column->hasSummary($query))
                    @foreach ($column->getSummarizers($query) as $summarizer)
                        {{ $summarizer->query($query)->selectedState($selectedState) }}
                    @endforeach
                @endif
            </td>
        @endif
    @endforeach

    @if ($placeholderColumns && $actions && in_array($actionsPosition, [RecordActionsPosition::AfterColumns, RecordActionsPosition::AfterCells]))
        <td></td>
    @endif

    @if ($placeholderColumns && $selectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
        <td></td>
    @endif
</tr>
