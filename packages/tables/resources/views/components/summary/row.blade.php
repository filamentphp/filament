@props([
    'actions' => false,
    'actionsPosition' => null,
    'columns',
    'extraHeadingColumn' => false,
    'groupsOnly' => false,
    'heading',
    'placeholderColumns' => true,
    'query',
    'selectionEnabled' => false,
    'strong' => false,
])

@php
    use Filament\Tables\Actions\Position as ActionsPosition;
@endphp

<x-filament-tables::row {{ $attributes->class([
    'bg-gray-500/5' => $strong,
]) }}>
    @if ($placeholderColumns && $actions && in_array($actionsPosition, [ActionsPosition::BeforeCells, ActionsPosition::BeforeColumns]))
        <td></td>
    @endif

    @if ($placeholderColumns && $selectionEnabled)
        <td></td>
    @endif

    @if ($placeholderColumns && $actions && $actionsPosition === ActionsPosition::BeforeColumns)
        <td></td>
    @endif

    @if ($extraHeadingColumn || $groupsOnly)
        <td @class([
            'align-top px-4 py-3 font-medium',
            'text-sm' => ! $strong,
            'text-base' => $strong,
        ])>
            {{ $heading }}
        </td>
    @else
        @php
            $headingColumnSpan = 1;

            foreach ($columns as $index => $column) {
                if ($index === array_key_first($columns)) {
                    continue;
                }

                if ($column->hasSummary()) {
                    break;
                }

                $headingColumnSpan++;
            }
        @endphp
    @endif

    @foreach ($columns as $column)
        @if (($loop->first || $extraHeadingColumn || $groupsOnly || ($loop->iteration > $headingColumnSpan)) && ($placeholderColumns || $column->hasSummary()))
            <td
                @if ($loop->first && (! $extraHeadingColumn) && (! $groupsOnly) && ($headingColumnSpan > 1))
                    colspan="{{ $headingColumnSpan }}"
                @endif
                @class([
                    "-space-y-3 align-top",
                    match ($column->getAlignment()) {
                        'start' => 'text-start',
                        'center' => 'text-center',
                        'end' => 'text-end',
                        'left' => 'text-left',
                        'right' => 'text-right',
                        'justify' => 'text-justify',
                        default => null,
                    },
                ])
            >
                @if ($loop->first && (! $extraHeadingColumn) && (! $groupsOnly))
                    <div @class([
                        'px-4 py-3 font-medium',
                        'text-sm' => ! $strong,
                        'text-base' => $strong,
                    ])>
                        {{ $heading }}
                    </div>
                @elseif ((! $placeholderColumns) || $column->hasSummary())
                    @foreach ($column->getSummary($query) as $summary)
                        {{ $summary }}
                    @endforeach
                @endif
            </td>
        @endif
    @endforeach

    @if ($placeholderColumns && $actions && $actionsPosition === ActionsPosition::AfterCells)
        <td></td>
    @endif
</x-filament-tables::row>
