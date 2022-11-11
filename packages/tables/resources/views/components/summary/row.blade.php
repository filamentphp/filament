@props([
    'actions',
    'actionsPosition',
    'columns',
    'heading',
    'isSelectionEnabled',
    'query',
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

    @foreach ($columns as $column)
        <td class="-space-y-3 align-top">
            @if ($loop->first)
                <div class="px-4 py-3 text-sm font-medium">
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
