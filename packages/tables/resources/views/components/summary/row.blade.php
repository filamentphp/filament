@props([
    'actions',
    'actionsPosition',
    'columns',
    'heading',
    'isGroupsOnly' => false,
    'isSelectionEnabled',
    'query',
    'strong' => false,
])

@php
    use Filament\Tables\Actions\Position as ActionsPosition;
@endphp

<x-filament-tables::row {{ $attributes->class([
    'bg-gray-500/5' => $strong,
]) }}>
    @if ($actions && in_array($actionsPosition, [ActionsPosition::BeforeCells, ActionsPosition::BeforeColumns]))
        <td></td>
    @endif

    @if ($isSelectionEnabled)
        <td></td>
    @endif

    @if ($actions && $actionsPosition === ActionsPosition::BeforeColumns)
        <td></td>
    @endif

    @if ($isGroupsOnly)
        <td @class([
            'align-top px-4 py-3 font-medium',
            'text-sm' => ! $strong,
            'text-base' => $strong,
        ])>
            {{ $heading }}
        </td>
    @endif

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

    @foreach ($columns as $column)
        @if ($loop->first || $isGroupsOnly || ($loop->iteration > $headingColumnSpan))
            <td
                @if ($loop->first && (! $isGroupsOnly) && ($headingColumnSpan > 1))
                    colspan="{{ $headingColumnSpan }}"
                @endif
                class="-space-y-3 align-top"
            >
                @if ($loop->first && (! $isGroupsOnly))
                    <div @class([
                        'px-4 py-3 font-medium',
                        'text-sm' => ! $strong,
                        'text-base' => $strong,
                    ])>
                        {{ $heading }}
                    </div>
                @elseif ($column->hasSummary())
                    @foreach ($column->getSummary($query) as $summary)
                        {{ $summary }}
                    @endforeach
                @endif
            </td>
        @endif
    @endforeach

    @if ($actions && $actionsPosition === ActionsPosition::AfterCells)
        <td></td>
    @endif
</x-filament-tables::row>
