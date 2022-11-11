@props([
    'actions',
    'actionsPosition',
    'columns',
    'isSelectionEnabled',
    'pluralModelLabel',
    'records',
])

@php
    use Filament\Tables\Actions\Position as ActionsPosition;
@endphp

<x-filament-tables::row class="bg-gray-500/5">
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
        <td>
            <div class="flex items-center w-full px-4 py-2 whitespace-nowrap space-x-1 rtl:space-x-reverse font-medium text-sm text-gray-600 dark:text-gray-300">
                @if ($loop->first)
                    <span class="font-bold">
                        {{ __('filament-tables::table.summary.heading') }}
                    </span>
                @elseif ($column->hasSummary())
                    {{ $column->getLabel() }}
                @endif
            </div>
        </td>
    @endforeach

    @if ($actions && $actionsPosition === ActionsPosition::AfterCells)
        <td></td>
    @endif
</x-filament-tables::row>

@if ($records instanceof \Illuminate\Contracts\Pagination\Paginator && $records->hasPages())
    <x-filament-tables::summary.row
        :actions="$actions"
        :actions-position="$actionsPosition"
        :columns="$columns"
        :heading="__('filament-tables::table.summary.subheadings.page', ['label' => $pluralModelLabel])"
        :is-selection-enabled="$isSelectionEnabled"
        :query="$this->applySortingToTableQuery(
            $this->getFilteredTableQuery()->forPage(
                page: $records->currentPage(),
                perPage: $records->perPage(),
            ),
        )"
    />
@endif

<x-filament-tables::summary.row
    :actions="$actions"
    :actions-position="$actionsPosition"
    :columns="$columns"
    :heading="__('filament-tables::table.summary.subheadings.all', ['label' => $pluralModelLabel])"
    :is-selection-enabled="$isSelectionEnabled"
    :query="$this->getFilteredTableQuery()"
/>
