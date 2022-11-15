@props([
    'actions',
    'actionsPosition',
    'columns',
    'isSelectionEnabled',
    'isGroupsOnly',
    'pluralModelLabel',
    'records',
])

@php
    use Filament\Tables\Actions\Position as ActionsPosition;

    $hasPageSummary = (! $isGroupsOnly) && $records instanceof \Illuminate\Contracts\Pagination\Paginator && $records->hasPages();
@endphp

@if ($hasPageSummary)
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
                        <span class="text-base font-medium">
                            {{ __('filament-tables::table.summary.heading', ['label' => $pluralModelLabel]) }}
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

    <x-filament-tables::summary.row
        :actions="$actions"
        :actions-position="$actionsPosition"
        :columns="$columns"
        :heading="__('filament-tables::table.summary.subheadings.page', ['label' => $pluralModelLabel])"
        :is-selection-enabled="$isSelectionEnabled"
        :query="$this->getPageTableSummaryQuery()"
    />
@endif

<x-filament-tables::summary.row
    :actions="$actions"
    :actions-position="$actionsPosition"
    :columns="$columns"
    :heading="__(($hasPageSummary ? 'filament-tables::table.summary.subheadings.all' : 'filament-tables::table.summary.heading'), ['label' => $pluralModelLabel])"
    :is-groups-only="$isGroupsOnly"
    :is-selection-enabled="$isSelectionEnabled"
    :query="$this->getAllTableSummaryQuery()"
    :strong="! $hasPageSummary"
/>
