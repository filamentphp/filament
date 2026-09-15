@php
    use Filament\Tables\Columns\Column;
@endphp

@php
    $sortableColumns = $isStackedOnMobile ? array_filter(
        $columns,
        fn (Column $column): bool => $column->isSortable(),
    ) : [];
@endphp

<table
    aria-label="{{ filled($tableAccessibleLabel = trim(strip_tags((string) $heading))) ? $tableAccessibleLabel : $pluralModelLabel }}"
    @class([
        'fi-ta-table',
        'fi-ta-table-stacked-on-mobile' => $isStackedOnMobile,
    ])
>
    <thead>
        @include('filament-tables::components.parts.content.table-stacked-header-row')

        @include('filament-tables::components.parts.content.table-column-groups-row')

        @include('filament-tables::components.parts.content.table-header-row')
    </thead>

    @if ($isColumnSearchVisible || count($records))
        <tbody
            @if ($isReorderable)
                x-on:end.stop="
                    $wire.reorderTable(
                        $event.target.sortable.toArray(),
                        $event.item.getAttribute('x-sortable-item'),
                    )
                "
                x-sortable
                data-sortable-animation-duration="{{ $table->getReorderAnimationDuration() }}"
            @endif
        >
            @include('filament-tables::components.parts.content.table-column-search-row')

            @if (count($records))
                @php
                    $isRecordRowStriped = false;
                    $previousRecord = null;
                    $previousRecordGroupKey = null;
                    $previousRecordGroupTitle = null;
                @endphp

                @foreach ($records as $record)
                    @php
                        $recordAction = $table->getRecordAction($record);
                        $recordKey = $table->getRecordKey($record);
                        $recordUrl = $table->getRecordUrl($record);
                        $openRecordUrlInNewTab = $table->shouldOpenRecordUrlInNewTab($record);
                        $recordGroupKey = $group?->getStringKey($record);
                        $recordGroupTitle = $group?->getTitle($record, $recordGroupKey);
                        $recordIsSelectable = $isSelectionEnabled && $table->isRecordSelectable($record);

                        $recordActions = $recordActionsByRecordKey[$recordKey]
                            ?? ($hasRecordActionsForAnyRecord ? $reduceVisibleRecordActions($record) : []);
                    @endphp

                    @if ((string) $recordGroupTitle !== (string) $previousRecordGroupTitle)
                        @include('filament-tables::components.parts.content.table-group-header-row')

                        @php
                            $isRecordRowStriped = false;
                        @endphp
                    @endif

                    @include('filament-tables::components.parts.content.table-row')

                    @php
                        $isRecordRowStriped = ! $isRecordRowStriped;
                        $previousRecord = $record;
                        $previousRecordGroupKey = $recordGroupKey;
                        $previousRecordGroupTitle = $recordGroupTitle;
                    @endphp
                @endforeach

                @if ($hasSummary && (! $isReordering) && filled($previousRecordGroupTitle) && $this->shouldRenderTrailingGroupedTableSummary($previousRecord))
                    @php
                        $groupColumn = $group->getColumn();
                        $groupScopedAllTableSummaryQuery = $group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord);
                    @endphp

                    <x-filament-tables::summary.row
                        :actions="$hasRecordActionsForAnyRecord"
                        :actions-position="$recordActionsPosition"
                        :columns="$columns"
                        :group-column="$groupColumn"
                        :groups-only="$isGroupsOnly"
                        :heading="$isGroupsOnly ? $previousRecordGroupTitle : __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $pluralModelLabel])"
                        :query="$groupScopedAllTableSummaryQuery"
                        :record-checkbox-position="$recordCheckboxPosition"
                        :selected-state="$groupedSummarySelectedState[$previousRecordGroupKey] ?? []"
                        :selection-enabled="$isSelectionEnabled"
                    />
                @endif

                @if ($hasSummary && (! $isReordering))
                    @php
                        $groupColumn = $group?->getColumn();
                    @endphp

                    <x-filament-tables::summary
                        :actions="$hasRecordActionsForAnyRecord"
                        :actions-position="$recordActionsPosition"
                        :all-table-summary="$hasAllTableSummary"
                        :columns="$columns"
                        :group-column="$groupColumn"
                        :groups-only="$isGroupsOnly"
                        :page-summary="$hasPageSummary"
                        :plural-model-label="$pluralModelLabel"
                        :record-checkbox-position="$recordCheckboxPosition"
                        :records="$records"
                        :selection-enabled="$isSelectionEnabled"
                    />
                @endif
            @endif
        </tbody>
    @endif

    @php
        // Fetched after the row partials rendered, since Blade renders a `Renderable` it passes into an `@include`.
        $contentFooter = $table->getContentFooter();
    @endphp

    @if (($records !== null) && count($records) && $contentFooter)
        <tfoot>
            <tr>
                {{
                    $contentFooter->with([
                        'columns' => $columns,
                        'records' => $records,
                    ])
                }}
            </tr>
        </tfoot>
    @endif
</table>
