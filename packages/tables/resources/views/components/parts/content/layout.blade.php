@php
    use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
    use Illuminate\View\ComponentAttributeBag;
@endphp

@include('filament-tables::components.parts.content.layout-header')

@if ($content)
    {{ $content->with(['records' => $records]) }}
@else
    <div
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
        aria-label="{{ $pluralModelLabel }}"
        role="list"
        {{
            (new FilamentComponentAttributeBag)
                ->when($contentGrid, fn (ComponentAttributeBag $attributes) => $attributes->grid($contentGrid))
                ->class([
                    'fi-ta-content',
                    'fi-ta-content-grid' => $contentGrid,
                    'fi-ta-content-grouped' => $this->getTableGrouping(),
                ])
        }}
    >
        @php
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
                $isRecordGroupCollapsible = $group?->isCollapsible();
                $recordIsSelectable = $isSelectionEnabled && $table->isRecordSelectable($record);

                $collapsibleColumnsLayout?->record($record)->recordKey($recordKey);
                $hasCollapsibleColumnsLayout = (bool) $collapsibleColumnsLayout?->isVisible();

                $recordActions = $recordActionsByRecordKey[$recordKey]
                    ?? ($hasRecordActionsForAnyRecord ? $reduceVisibleRecordActions($record) : []);
            @endphp

            @include('filament-tables::components.parts.content.layout-group-header')

            @include('filament-tables::components.parts.content.layout-record')

            @php
                $previousRecordGroupKey = $recordGroupKey;
                $previousRecordGroupTitle = $recordGroupTitle;
                $previousRecord = $record;
            @endphp
        @endforeach

        @if ($hasSummary && (! $isReordering) && filled($previousRecordGroupTitle) && $this->shouldRenderTrailingGroupedTableSummary($previousRecord))
            <table class="fi-ta-table">
                <tbody>
                    @php
                        $groupScopedAllTableSummaryQuery = $group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord);
                    @endphp

                    <x-filament-tables::summary.row
                        :columns="$columns"
                        extra-heading-column
                        :heading="__('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $pluralModelLabel])"
                        :placeholder-columns="false"
                        :query="$groupScopedAllTableSummaryQuery"
                        :selected-state="$groupedSummarySelectedState[$previousRecordGroupKey] ?? []"
                    />
                </tbody>
            </table>
        @endif
    </div>
@endif

@if (($content || $hasColumnsLayout) && $contentFooter)
    {{
        $contentFooter->with([
            'columns' => $columns,
            'records' => $records,
        ])
    }}
@endif

@if ($hasTopLevelSummary && (! $isReordering))
    <table class="fi-ta-table">
        <tbody>
            <x-filament-tables::summary
                :all-table-summary="$hasAllTableSummary"
                :columns="$columns"
                extra-heading-column
                :page-summary="$hasPageSummary"
                :placeholder-columns="false"
                :plural-model-label="$pluralModelLabel"
                :records="$records"
            />
        </tbody>
    </table>
@endif
