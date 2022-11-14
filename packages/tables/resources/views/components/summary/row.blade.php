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

<x-filament-tables::row {{ $attributes }}>
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

    @foreach ($columns as $column)
        <td class="-space-y-3 align-top">
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
    @endforeach

    @if ($actions && $actionsPosition === ActionsPosition::AfterCells)
        <td></td>
    @endif
</x-filament-tables::row>
