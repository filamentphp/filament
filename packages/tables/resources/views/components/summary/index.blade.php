@props([
    'actions' => false,
    'actionsPosition' => null,
    'columns',
    'extraHeadingColumn' => false,
    'groupsOnly' => false,
    'placeholderColumns' => true,
    'pluralModelLabel',
    'records',
    'recordCheckboxPosition' => null,
    'selectionEnabled' => false,
])

@php
    use Filament\Tables\Actions\Position as ActionsPosition;
    use Filament\Tables\Actions\RecordCheckboxPosition;

    $hasPageSummary = (! $groupsOnly) && $records instanceof \Illuminate\Contracts\Pagination\Paginator && $records->hasPages();
@endphp

@if ($hasPageSummary)
    <x-filament-tables::row
        class="filament-tables-summary-header-row bg-gray-500/5"
    >
        @if ($placeholderColumns && $actions && in_array($actionsPosition, [ActionsPosition::BeforeCells, ActionsPosition::BeforeColumns]))
            <td></td>
        @endif

        @if ($placeholderColumns && $selectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
            <td></td>
        @endif

        @if ($extraHeadingColumn)
            <td
                class="whitespace-nowrap px-4 py-2 text-base font-medium text-gray-600 dark:text-gray-300"
            >
                {{ __('filament-tables::table.summary.heading', ['label' => $pluralModelLabel]) }}
            </td>
        @endif

        @foreach ($columns as $column)
            @if ($placeholderColumns || $column->hasSummary())
                @php
                    $hasColumnHeaderLabel = (! $placeholderColumns) || $column->hasSummary();
                @endphp

                <td
                    {{
                        $column->getExtraHeaderAttributeBag()->class([
                            'px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300',
                            'whitespace-nowrap' => ! $column->isHeaderWrapped(),
                            'whitespace-normal' => $column->isHeaderWrapped(),
                            match ($column->getAlignment()) {
                                'start' => 'text-start',
                                'center' => 'text-center',
                                'end' => 'text-end',
                                'left' => 'text-left',
                                'right' => 'text-right',
                                'justify' => 'text-justify',
                                default => null,
                            } => (! ($loop->first && (! $extraHeadingColumn))) && $hasColumnHeaderLabel,
                        ])
                    }}
                >
                    @if ($loop->first && (! $extraHeadingColumn))
                        <span class="text-base">
                            {{ __('filament-tables::table.summary.heading', ['label' => $pluralModelLabel]) }}
                        </span>
                    @elseif ($hasColumnHeaderLabel)
                        {{ $column->getLabel() }}
                    @endif
                </td>
            @endif
        @endforeach

        @if ($placeholderColumns && $actions && in_array($actionsPosition, [ActionsPosition::AfterColumns, ActionsPosition::AfterCells]))
            <td></td>
        @endif

        @if ($placeholderColumns && $selectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
            <td></td>
        @endif
    </x-filament-tables::row>

    @php
        $query = $this->getPageTableSummaryQuery();
        $selectedState = $this->getTableSummarySelectedState($query)[0] ?? [];
    @endphp

    <x-filament-tables::summary.row
        class="filament-tables-page-summary-row"
        :actions="$actions"
        :actions-position="$actionsPosition"
        :columns="$columns"
        :extra-heading-column="$extraHeadingColumn"
        :heading="__('filament-tables::table.summary.subheadings.page', ['label' => $pluralModelLabel])"
        :selection-enabled="$selectionEnabled"
        :placeholder-columns="$placeholderColumns"
        :query="$query"
        :selected-state="$selectedState"
        :record-checkbox-position="$recordCheckboxPosition"
    />
@endif

@php
    $query = $this->getAllTableSummaryQuery();
    $selectedState = $this->getTableSummarySelectedState($query)[0] ?? [];
@endphp

<x-filament-tables::summary.row
    class="filament-tables-total-summary-row"
    :actions="$actions"
    :actions-position="$actionsPosition"
    :columns="$columns"
    :extra-heading-column="$extraHeadingColumn"
    :heading="__(($hasPageSummary ? 'filament-tables::table.summary.subheadings.all' : 'filament-tables::table.summary.heading'), ['label' => $pluralModelLabel])"
    :groups-only="$groupsOnly"
    :selection-enabled="$selectionEnabled"
    :query="$query"
    :placeholder-columns="$placeholderColumns"
    :selected-state="$selectedState"
    :strong="! $hasPageSummary"
    :record-checkbox-position="$recordCheckboxPosition"
/>
