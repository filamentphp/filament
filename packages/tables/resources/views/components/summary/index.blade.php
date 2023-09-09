@props([
    'actions' => false,
    'actionsPosition' => null,
    'columns',
    'extraHeadingColumn' => false,
    'groupsOnly' => false,
    'placeholderColumns' => true,
    'pluralModelLabel',
    'recordCheckboxPosition' => null,
    'records',
    'selectionEnabled' => false,
])

@php
    use Filament\Support\Enums\Alignment;
    use Filament\Tables\Enums\ActionsPosition;
    use Filament\Tables\Enums\RecordCheckboxPosition;

    $hasPageSummary = (! $groupsOnly) && $records instanceof \Illuminate\Contracts\Pagination\Paginator && $records->hasPages();
@endphp

@if ($hasPageSummary)
    <x-filament-tables::row
        class="fi-ta-summary-header-row bg-gray-50 dark:bg-white/5"
    >
        @if ($placeholderColumns && $actions && in_array($actionsPosition, [ActionsPosition::BeforeCells, ActionsPosition::BeforeColumns]))
            <td></td>
        @endif

        @if ($placeholderColumns && $selectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
            <td></td>
        @endif

        @if ($extraHeadingColumn)
            <x-filament-tables::summary.header-cell>
                {{ __('filament-tables::table.summary.heading', ['label' => $pluralModelLabel]) }}
            </x-filament-tables::summary.header-cell>
        @endif

        @foreach ($columns as $column)
            @if ($placeholderColumns || $column->hasSummary())
                @php
                    $hasColumnHeaderLabel = (! $placeholderColumns) || $column->hasSummary();
                @endphp

                <x-filament-tables::summary.header-cell
                    :attributes="
                        \Filament\Support\prepare_inherited_attributes($column->getExtraHeaderAttributeBag())
                            ->class([
                                'whitespace-nowrap' => ! $column->isHeaderWrapped(),
                                'whitespace-normal' => $column->isHeaderWrapped(),
                                match ($column->getAlignment()) {
                                    Alignment::Start, 'start' => 'text-start',
                                    Alignment::Center, 'center' => 'text-center',
                                    Alignment::End, 'end' => 'text-end',
                                    Alignment::Left, 'left' => 'text-left',
                                    Alignment::Right, 'right' => 'text-right',
                                    Alignment::Justify, 'justify' => 'text-justify',
                                    default => null,
                                } => (! ($loop->first && (! $extraHeadingColumn))) && $hasColumnHeaderLabel,
                            ])
                    "
                >
                    @if ($loop->first && (! $extraHeadingColumn))
                        {{ __('filament-tables::table.summary.heading', ['label' => $pluralModelLabel]) }}
                    @elseif ($hasColumnHeaderLabel)
                        {{ $column->getLabel() }}
                    @endif
                </x-filament-tables::summary.header-cell>
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
        :actions="$actions"
        :actions-position="$actionsPosition"
        :columns="$columns"
        :extra-heading-column="$extraHeadingColumn"
        :heading="__('filament-tables::table.summary.subheadings.page', ['label' => $pluralModelLabel])"
        :placeholder-columns="$placeholderColumns"
        :query="$query"
        :record-checkbox-position="$recordCheckboxPosition"
        :selected-state="$selectedState"
        :selection-enabled="$selectionEnabled"
    />
@endif

@php
    $query = $this->getAllTableSummaryQuery();
    $selectedState = $this->getTableSummarySelectedState($query)[0] ?? [];
@endphp

<x-filament-tables::summary.row
    :actions="$actions"
    :actions-position="$actionsPosition"
    :columns="$columns"
    :extra-heading-column="$extraHeadingColumn"
    :groups-only="$groupsOnly"
    :heading="__(($hasPageSummary ? 'filament-tables::table.summary.subheadings.all' : 'filament-tables::table.summary.heading'), ['label' => $pluralModelLabel])"
    :placeholder-columns="$placeholderColumns"
    :query="$query"
    :record-checkbox-position="$recordCheckboxPosition"
    :selected-state="$selectedState"
    :selection-enabled="$selectionEnabled"
    @class([
        'bg-gray-50 dark:bg-white/5' => ! $hasPageSummary,
    ])
/>
