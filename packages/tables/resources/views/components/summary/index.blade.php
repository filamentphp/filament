@props([
    'actions' => false,
    'actionsPosition' => null,
    'columns',
    'extraHeadingColumn' => false,
    'groupColumn' => null,
    'groupsOnly' => false,
    'placeholderColumns' => true,
    'pluralModelLabel',
    'recordCheckboxPosition' => null,
    'records',
    'selectionEnabled' => false,
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

    $hasPageSummary = (! $groupsOnly) && $records instanceof \Illuminate\Contracts\Pagination\Paginator && $records->hasPages();

    $pageTableSummaryQuery = $hasPageSummary ? $this->getPageTableSummaryQuery() : null;
    $allTableSummaryQuery = $this->getAllTableSummaryQuery();
@endphp

@if ($hasPageSummary)
    <tr class="fi-ta-row fi-ta-summary-header-row fi-striped">
        @if ($placeholderColumns && $actions && in_array($actionsPosition, [RecordActionsPosition::BeforeCells, RecordActionsPosition::BeforeColumns]))
            <td></td>
        @endif

        @if ($placeholderColumns && $selectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
            <td></td>
        @endif

        @if ($extraHeadingColumn)
            <td class="fi-ta-cell fi-ta-summary-header-cell">
                {{ __('filament-tables::table.summary.heading', ['label' => $pluralModelLabel]) }}
            </td>
        @endif

        @foreach ($columns as $column)
            @php
                $columnHasSummary = ($pageTableSummaryQuery && $column->hasSummary($pageTableSummaryQuery)) || $column->hasSummary($allTableSummaryQuery);
            @endphp

            @if ($placeholderColumns || $columnHasSummary)
                @php
                    $alignment = $column->getAlignment() ?? Alignment::Start;

                    if (! $alignment instanceof Alignment) {
                        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
                    }

                    $hasColumnHeaderLabel = (! $placeholderColumns) || $columnHasSummary;
                @endphp

                <td
                    {{
                        $column->getExtraHeaderAttributeBag()->class([
                            'fi-ta-cell fi-ta-summary-header-cell',
                            'fi-wrapped' => $column->canHeaderWrap(),
                            (($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : '')) => (! ($loop->first && (! $extraHeadingColumn))) && $hasColumnHeaderLabel,
                        ])
                    }}
                >
                    @if ($loop->first && (! $extraHeadingColumn))
                        {{ __('filament-tables::table.summary.heading', ['label' => $pluralModelLabel]) }}
                    @elseif ($hasColumnHeaderLabel)
                        {{ $column->getLabel() }}
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

    @php
        $selectedState = $this->getTableSummarySelectedState($pageTableSummaryQuery)[0] ?? [];
    @endphp

    <x-filament-tables::summary.row
        :actions="$actions"
        :actions-position="$actionsPosition"
        :columns="$columns"
        :extra-heading-column="$extraHeadingColumn"
        :heading="__('filament-tables::table.summary.subheadings.page', ['label' => $pluralModelLabel])"
        :placeholder-columns="$placeholderColumns"
        :query="$pageTableSummaryQuery"
        :record-checkbox-position="$recordCheckboxPosition"
        :selected-state="$selectedState"
        :selection-enabled="$selectionEnabled"
    />
@endif

@php
    $selectedState = $this->getTableSummarySelectedState($allTableSummaryQuery)[0] ?? [];
@endphp

<x-filament-tables::summary.row
    :actions="$actions"
    :actions-position="$actionsPosition"
    :columns="$columns"
    :extra-heading-column="$extraHeadingColumn"
    :groups-only="$groupsOnly"
    :heading="__(($hasPageSummary ? 'filament-tables::table.summary.subheadings.all' : 'filament-tables::table.summary.heading'), ['label' => $pluralModelLabel])"
    :placeholder-columns="$placeholderColumns"
    :query="$allTableSummaryQuery"
    :record-checkbox-position="$recordCheckboxPosition"
    :selected-state="$selectedState"
    :selection-enabled="$selectionEnabled"
    @class([
        'fi-striped' => ! $hasPageSummary,
    ])
/>
