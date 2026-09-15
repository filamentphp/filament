@php
    use Filament\Actions\BulkAction;
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\IconSize;
    use Filament\Tables\Components\TablePageCheckbox;
    use Filament\Tables\Components\TableSortingSettings;
    use Filament\Tables\Table;
    use Illuminate\Contracts\Pagination\LengthAwarePaginator;
    use Illuminate\Database\Query\Builder;

    $defaultRecordActions = $table->getRecordActions();
    $flatRecordActionsCount = count($table->getFlatRecordActions());
    $recordActionsAlignment = $table->getRecordActionsAlignment();
    $recordActionsPosition = $table->getRecordActionsPosition();
    $recordActionsColumnLabel = $table->getRecordActionsColumnLabel();

    if (! $recordActionsAlignment instanceof Alignment) {
        $recordActionsAlignment = filled($recordActionsAlignment) ? (Alignment::tryFrom($recordActionsAlignment) ?? $recordActionsAlignment) : null;
    }

    $isSelectionDisabled = $table->isSelectionDisabled();
    $maxSelectableRecords = $table->getMaxSelectableRecords();
    $columns = $table->getVisibleColumns();
    $collapsibleColumnsLayout = $table->getCollapsibleColumnsLayout();
    $columnsLayout = $table->getColumnsLayout();
    // The content and footer views are fetched by the partials that render them: Blade
    // would render a `Renderable` passed into an `@include` before the partial runs.
    $hasCustomContent = $table->getContent() !== null;
    $contentGrid = $table->getContentGrid();
    $hasColumnGroups = $table->hasColumnGroups();
    $hasColumnsLayout = $table->hasColumnsLayout();
    $hasPageSummary = $table->hasPageSummary();
    $hasAllTableSummary = $table->hasAllTableSummary();
    $hasSummary = $table->hasSummary($this->getAllTableSummaryQuery());
    $hasTopLevelSummary = $hasSummary && ($hasPageSummary || $hasAllTableSummary);
    $heading = $table->getHeading();
    $group = $table->getGrouping();
    $toolbarActions = $table->getVisibleToolbarActions();

    $hasNonBulkToolbarAction = $table->hasNonBulkToolbarAction();

    $isGroupsOnly = $table->isGroupsOnly() && $group;
    $isReorderable = $table->isReorderable();
    $isReordering = $table->isReordering();
    $areGroupsCollapsedByDefault = $table->areGroupsCollapsedByDefault();
    $isColumnSearchVisible = $table->isSearchableByColumn();
    $isSearchOnBlur = $table->isSearchOnBlur();
    $isSelectionEnabled = $table->canSelectRecords();
    $selectsCurrentPageOnly = $table->selectsCurrentPageOnly();
    $selectsGroupsOnly = $table->selectsGroupsOnly();
    $recordCheckboxPosition = $table->getRecordCheckboxPosition();
    $isStriped = $table->isStriped();
    $isStackedOnMobile = $table->isStackedOnMobile();
    $isLoaded = $table->isLoaded();
    $hasHeader = $table->hasHeader();

    // https://github.com/filamentphp/filament/pull/19787
    $headerVisibilityMode = $table->getHeaderVisibilityMode();
    $secondLevelHeadingTag = $table->getSecondLevelHeadingTag();
    $pluralModelLabel = $table->getPluralModelLabel();
    $records = $isLoaded ? $table->getRecords() : null;
    $hasContentLayout = $hasCustomContent || $hasColumnsLayout;
    $searchDebounce = $table->getSearchDebounce();
    $columnsCount = count($columns);
    $page = $this->getTablePage();
    $defaultSortOptionLabel = $table->getDefaultSortOptionLabel();
    $sortDirection = $table->getSortDirection();

    $reduceVisibleRecordActions = function ($record) use ($defaultRecordActions): array {
        return array_reduce(
            $defaultRecordActions,
            function (array $carry, $action) use ($record): array {
                $action = $action->getClone();

                if (! $action instanceof BulkAction) {
                    $action->record($record);
                }

                if ($action->isHidden()) {
                    return $carry;
                }

                $carry[] = $action;

                return $carry;
            },
            initial: [],
        );
    };

    $recordActionsByRecordKey = [];
    $hasRecordActionsForAnyRecord = false;

    // Determine whether the record actions column should be rendered by scanning
    // records until one exposes a visible action. Every record that gets checked
    // is cached so the row loop below never re-evaluates its visibility.
    if (($records !== null) && (! $isReordering)) {
        foreach ($records as $record) {
            $recordActionsByRecordKey[$table->getRecordKey($record)] = $currentRecordActions = $reduceVisibleRecordActions($record);

            if ($currentRecordActions !== []) {
                $hasRecordActionsForAnyRecord = true;

                break;
            }
        }
    }

    if ($hasRecordActionsForAnyRecord && (! $isReordering)) {
        $columnsCount++;
    }

    if ($isSelectionEnabled || $isReordering) {
        $columnsCount++;
    }

    if ($group) {
        $groupedSummarySelectedState = $this->getTableSummarySelectedState($this->getAllTableSummaryQuery(), modifyQueryUsing: fn (Builder $query) => $group->groupQuery($query, model: $table->getQuery()->getModel()));
    }

    $loadingTargetsWireTarget = implode(',', Table::LOADING_TARGETS);

    // A layout that places `TablePageCheckbox` or `TableSortingSettings` elsewhere must not repeat them in the content header.
    $hasPageCheckboxInContentHeader = ! $table->hasLayoutPart(TablePageCheckbox::class);
    $hasSortingSettingsInContentHeader = ! $table->hasLayoutPart(TableSortingSettings::class);
@endphp

@if ((! $hasContentLayout) || ($records === null) || count($records))
    <div
        @if ((! $isReordering) && ($pollingInterval = $table->getPollingInterval()))
            wire:poll.{{ $pollingInterval }}
        @endif
        class="fi-ta-content-ctn fi-fixed-positioning-context"
    >
        @if ($records !== null)
            @php
                // The total across all pages, not the current page's count — and since the total is
                // stable across pages, the live region only announces when the result set really
                // changes, not on every pagination click. Non-length-aware paginators fall back to
                // the page count.
                $resultCount = ($records instanceof LengthAwarePaginator)
                    ? $records->total()
                    : count($records);
            @endphp

            <div
                role="status"
                aria-live="polite"
                aria-atomic="true"
                class="fi-sr-only"
            >
                {{ trans_choice('filament-tables::table.result_count', $resultCount, ['count' => $resultCount]) }}
            </div>
        @endif

        @if ($hasContentLayout && ($records !== null) && count($records))
            @include('filament-tables::components.parts.content.layout')
        @elseif ((! $hasContentLayout) && ($records !== null))
            @include('filament-tables::components.parts.content.table')
        @elseif ($records === null)
            <div
                role="status"
                aria-busy="true"
                aria-live="polite"
                class="fi-ta-table-loading-ctn"
            >
                {{ \Filament\Support\generate_loading_indicator_html(size: IconSize::TwoExtraLarge) }}

                <span class="fi-sr-only">
                    {{ __('filament-tables::table.loading') }}
                </span>
            </div>
        @endif
    </div>
@endif
