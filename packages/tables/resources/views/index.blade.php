@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Facades\FilamentView;
    use Filament\Tables\Enums\ActionsPosition;
    use Filament\Tables\Enums\FiltersLayout;
    use Filament\Tables\Enums\RecordCheckboxPosition;

    $actions = $getActions();
    $actionsAlignment = $getActionsAlignment();
    $actionsPosition = $getActionsPosition();
    $actionsColumnLabel = $getActionsColumnLabel();
    $columns = $getVisibleColumns();
    $collapsibleColumnsLayout = $getCollapsibleColumnsLayout();
    $content = $getContent();
    $contentGrid = $getContentGrid();
    $contentFooter = $getContentFooter();
    $filterIndicators = [
        ...($hasSearch() ? ['resetTableSearch' => $getSearchIndicator()] : []),
        ...collect($getColumnSearchIndicators())
            ->mapWithKeys(fn (string $indicator, string $column): array => [
                "resetTableColumnSearch('{$column}')" => $indicator,
            ])
            ->all(),
        ...array_reduce(
            $getFilters(),
            fn (array $carry, \Filament\Tables\Filters\BaseFilter $filter): array => [
                ...$carry,
                ...collect($filter->getIndicators())
                    ->mapWithKeys(fn (string $label, int | string $field) => [
                        "removeTableFilter('{$filter->getName()}'" . (is_string($field) ? ' , \'' . $field . '\'' : null) . ')' => $label,
                    ])
                    ->all(),
            ],
            [],
        ),
    ];
    $hasColumnsLayout = $hasColumnsLayout();
    $hasSummary = $hasSummary();
    $header = $getHeader();
    $headerActions = array_filter(
        $getHeaderActions(),
        fn (\Filament\Tables\Actions\Action | \Filament\Tables\Actions\BulkAction | \Filament\Tables\Actions\ActionGroup $action): bool => $action->isVisible(),
    );
    $headerActionsPosition = $getHeaderActionsPosition();
    $heading = $getHeading();
    $group = $getGrouping();
    $bulkActions = array_filter(
        $getBulkActions(),
        fn (\Filament\Tables\Actions\BulkAction | \Filament\Tables\Actions\ActionGroup $action): bool => $action->isVisible(),
    );
    $groups = $getGroups();
    $description = $getDescription();
    $isGroupsOnly = $isGroupsOnly() && $group;
    $isReorderable = $isReorderable();
    $isReordering = $isReordering();
    $isColumnSearchVisible = $isSearchableByColumn();
    $isGlobalSearchVisible = $isSearchable();
    $isSelectionEnabled = $isSelectionEnabled();
    $recordCheckboxPosition = $getRecordCheckboxPosition();
    $isStriped = $isStriped();
    $isLoaded = $isLoaded();
    $hasFilters = $isFilterable();
    $filtersLayout = $getFiltersLayout();
    $filtersTriggerAction = $getFiltersTriggerAction();
    $hasFiltersDialog = $hasFilters && in_array($filtersLayout, [FiltersLayout::Dropdown, FiltersLayout::Modal]);
    $hasFiltersAboveContent = $hasFilters && in_array($filtersLayout, [FiltersLayout::AboveContent, FiltersLayout::AboveContentCollapsible]);
    $hasFiltersAboveContentCollapsible = $hasFilters && ($filtersLayout === FiltersLayout::AboveContentCollapsible);
    $hasFiltersBelowContent = $hasFilters && ($filtersLayout === FiltersLayout::BelowContent);
    $hasColumnToggleDropdown = $hasToggleableColumns();
    $hasHeader = $header || $heading || $description || ($headerActions && (! $isReordering)) || $isReorderable || count($groups) || $isGlobalSearchVisible || $hasFilters || count($filterIndicators) || $hasColumnToggleDropdown;
    $hasHeaderToolbar = $isReorderable || count($groups) || $isGlobalSearchVisible || $hasFiltersDialog || $hasColumnToggleDropdown;
    $pluralModelLabel = $getPluralModelLabel();
    $records = $isLoaded ? $getRecords() : null;
    $allSelectableRecordsCount = ($isSelectionEnabled && $isLoaded) ? $getAllSelectableRecordsCount() : null;
    $columnsCount = count($columns);
    $reorderRecordsTriggerAction = $getReorderRecordsTriggerAction($isReordering);
    $toggleColumnsTriggerAction = $getToggleColumnsTriggerAction();

    if (count($actions) && (! $isReordering)) {
        $columnsCount++;
    }

    if ($isSelectionEnabled || $isReordering) {
        $columnsCount++;
    }

    if ($group) {
        $groupedSummarySelectedState = $this->getTableSummarySelectedState($this->getAllTableSummaryQuery(), modifyQueryUsing: fn (\Illuminate\Database\Query\Builder $query) => $group->groupQuery($query, model: $getQuery()->getModel()));
    }

    $getHiddenClasses = function (Filament\Tables\Columns\Column $column): ?string {
        if ($breakpoint = $column->getHiddenFrom()) {
            return match ($breakpoint) {
                'sm' => 'sm:hidden',
                'md' => 'md:hidden',
                'lg' => 'lg:hidden',
                'xl' => 'xl:hidden',
                '2xl' => '2xl:hidden',
            };
        }

        if ($breakpoint = $column->getVisibleFrom()) {
            return match ($breakpoint) {
                'sm' => 'hidden sm:table-cell',
                'md' => 'hidden md:table-cell',
                'lg' => 'hidden lg:table-cell',
                'xl' => 'hidden xl:table-cell',
                '2xl' => 'hidden 2xl:table-cell',
            };
        }

        return null;
    };
@endphp

<div
    @if (! $isLoaded)
        wire:init="loadTable"
    @endif
    x-ignore
    @if (FilamentView::hasSpaMode())
        ax-load="visible"
    @else
        ax-load
    @endif
    ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('table', 'filament/tables') }}"
    x-data="table"
    @class([
        'fi-ta',
        'animate-pulse' => $records === null,
    ])
>
    <x-filament-tables::container>
        <div
            @if (! $hasHeader) x-cloak @endif
            x-bind:hidden="! (@js($hasHeader) || (selectedRecords.length && @js(count($bulkActions))))"
            x-show="@js($hasHeader) || (selectedRecords.length && @js(count($bulkActions)))"
            class="fi-ta-header-ctn divide-y divide-gray-200 dark:divide-white/10"
        >
            @if ($header)
                {{ $header }}
            @elseif (($heading || $description || $headerActions) && ! $isReordering)
                <x-filament-tables::header
                    :actions="$isReordering ? [] : $headerActions"
                    :actions-position="$headerActionsPosition"
                    :description="$description"
                    :heading="$heading"
                />
            @endif

            @if ($hasFiltersAboveContent)
                <div
                    x-data="{ areFiltersOpen: @js(! $hasFiltersAboveContentCollapsible) }"
                    @class([
                        'grid gap-y-3 px-4 py-4 sm:px-6',
                    ])
                >
                    @if ($hasFiltersAboveContentCollapsible)
                        <span
                            x-on:click="areFiltersOpen = ! areFiltersOpen"
                            class="ms-auto"
                        >
                            {{ $filtersTriggerAction->badge(count(\Illuminate\Support\Arr::flatten($filterIndicators))) }}
                        </span>
                    @endif

                    <x-filament-tables::filters
                        :form="$getFiltersForm()"
                        x-cloak
                        x-show="areFiltersOpen"
                    />
                </div>
            @endif

            <div
                @if (! $hasHeaderToolbar) x-cloak @endif
                x-show="@js($hasHeaderToolbar) || (selectedRecords.length && @js(count($bulkActions)))"
                class="fi-ta-header-toolbar flex items-center justify-between gap-x-4 px-4 py-3 sm:px-6"
            >
                {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.start', scopes: static::class) }}

                <div class="flex shrink-0 items-center gap-x-4">
                    {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.reorder-trigger.before', scopes: static::class) }}

                    @if ($isReorderable)
                        <span x-show="! selectedRecords.length">
                            {{ $reorderRecordsTriggerAction }}
                        </span>
                    @endif

                    {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.reorder-trigger.after', scopes: static::class) }}

                    @if ((! $isReordering) && count($bulkActions))
                        <x-filament-tables::actions
                            :actions="$bulkActions"
                            x-cloak="x-cloak"
                            x-show="selectedRecords.length"
                        />
                    @endif

                    {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.grouping-selector.before', scopes: static::class) }}

                    @if (count($groups))
                        <x-filament-tables::groups
                            :dropdown-on-desktop="$areGroupsInDropdownOnDesktop()"
                            :groups="$groups"
                            :trigger-action="$getGroupRecordsTriggerAction()"
                        />
                    @endif

                    {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.grouping-selector.after', scopes: static::class) }}
                </div>

                @if ($isGlobalSearchVisible || $hasFiltersDialog || $hasColumnToggleDropdown)
                    <div class="ms-auto flex items-center gap-x-4">
                        {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.search.before', scopes: static::class) }}

                        @if ($isGlobalSearchVisible)
                            <x-filament-tables::search-field
                                :placeholder="$getSearchPlaceholder()"
                            />
                        @endif

                        {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.search.after', scopes: static::class) }}

                        @if ($hasFiltersDialog || $hasColumnToggleDropdown)
                            @if ($hasFiltersDialog)
                                <x-filament-tables::filters.dialog
                                    :form="$getFiltersForm()"
                                    :indicators-count="count(\Illuminate\Support\Arr::flatten($filterIndicators))"
                                    :layout="$filtersLayout"
                                    :max-height="$getFiltersFormMaxHeight()"
                                    :trigger-action="$filtersTriggerAction"
                                    :width="$getFiltersFormWidth()"
                                />
                            @endif

                            {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.toggle-column-trigger.before', scopes: static::class) }}

                            @if ($hasColumnToggleDropdown)
                                <x-filament-tables::column-toggle.dropdown
                                    :form="$getColumnToggleForm()"
                                    :max-height="$getColumnToggleFormMaxHeight()"
                                    :trigger-action="$toggleColumnsTriggerAction"
                                    :width="$getColumnToggleFormWidth()"
                                />
                            @endif

                            {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.toggle-column-trigger.after', scopes: static::class) }}
                        @endif
                    </div>
                @endif

                {{ \Filament\Support\Facades\FilamentView::renderHook('tables::toolbar.end') }}
            </div>
        </div>

        @if ($isReordering)
            <x-filament-tables::reorder.indicator :colspan="$columnsCount" />
        @elseif ($isSelectionEnabled && $isLoaded)
            <x-filament-tables::selection.indicator
                :all-selectable-records-count="$allSelectableRecordsCount"
                :colspan="$columnsCount"
                x-bind:hidden="! selectedRecords.length"
                x-show="selectedRecords.length"
            />
        @endif

        @if (count($filterIndicators))
            <x-filament-tables::filters.indicators
                :indicators="$filterIndicators"
            />
        @endif

        <div
            @if ($pollingInterval = $getPollingInterval())
                wire:poll.{{ $pollingInterval }}
            @endif
            @class([
                'fi-ta-content relative divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:border-t-white/10',
                '!border-t-0' => ! $hasHeader,
            ])
        >
            @if (($content || $hasColumnsLayout) && ($records !== null) && count($records))
                @if (! $isReordering)
                    @php
                        $sortableColumns = array_filter(
                            $columns,
                            fn (\Filament\Tables\Columns\Column $column): bool => $column->isSortable(),
                        );
                    @endphp

                    @if ($isSelectionEnabled || count($sortableColumns))
                        <div
                            class="flex items-center gap-4 gap-x-6 bg-gray-50 px-4 dark:bg-white/5 sm:px-6"
                        >
                            @if ($isSelectionEnabled && (! $isReordering))
                                <x-filament-tables::selection.checkbox
                                    :label="__('filament-tables::table.fields.bulk_select_page.label')"
                                    x-bind:checked="
                                        const recordsOnPage = getRecordsOnPage()

                                        if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                                            $el.checked = true

                                            return 'checked'
                                        }

                                        $el.checked = false

                                        return null
                                    "
                                    x-on:click="toggleSelectRecordsOnPage"
                                    class="my-4"
                                />
                            @endif

                            @if (count($sortableColumns))
                                <div
                                    x-data="{
                                        column: $wire.$entangle('tableSortColumn', true),
                                        direction: $wire.$entangle('tableSortDirection', true),
                                    }"
                                    x-init="
                                        $watch('column', function (newColumn, oldColumn) {
                                            if (! newColumn) {
                                                direction = null

                                                return
                                            }

                                            if (oldColumn) {
                                                return
                                            }

                                            direction = 'asc'
                                        })
                                    "
                                    class="flex gap-x-3 py-3"
                                >
                                    <label>
                                        <x-filament::input.wrapper
                                            :prefix="__('filament-tables::table.sorting.fields.column.label')"
                                        >
                                            <x-filament::input.select
                                                x-model="column"
                                            >
                                                <option value="">-</option>

                                                @foreach ($sortableColumns as $column)
                                                    <option
                                                        value="{{ $column->getName() }}"
                                                    >
                                                        {{ $column->getLabel() }}
                                                    </option>
                                                @endforeach
                                            </x-filament::input.select>
                                        </x-filament::input.wrapper>
                                    </label>

                                    <label x-cloak x-show="column">
                                        <span class="sr-only">
                                            {{ __('filament-tables::table.sorting.fields.direction.label') }}
                                        </span>

                                        <x-filament::input.wrapper>
                                            <x-filament::input.select
                                                x-model="direction"
                                            >
                                                <option value="asc">
                                                    {{ __('filament-tables::table.sorting.fields.direction.options.asc') }}
                                                </option>

                                                <option value="desc">
                                                    {{ __('filament-tables::table.sorting.fields.direction.options.desc') }}
                                                </option>
                                            </x-filament::input.select>
                                        </x-filament::input.wrapper>
                                    </label>
                                </div>
                            @endif
                        </div>
                    @endif
                @endif

                @if ($content)
                    {{ $content->with(['records' => $records]) }}
                @else
                    <x-filament::grid
                        :default="$contentGrid['default'] ?? 1"
                        :sm="$contentGrid['sm'] ?? null"
                        :md="$contentGrid['md'] ?? null"
                        :lg="$contentGrid['lg'] ?? null"
                        :xl="$contentGrid['xl'] ?? null"
                        :two-xl="$contentGrid['2xl'] ?? null"
                        x-on:end.stop="$wire.reorderTable($event.target.sortable.toArray())"
                        x-sortable
                        @class([
                            'fi-ta-content-grid gap-4 p-4 sm:px-6' => $contentGrid,
                            'pt-0' => $contentGrid && $this->getTableGrouping(),
                            'gap-y-px bg-gray-200 dark:bg-white/5' => ! $contentGrid,
                        ])
                    >
                        @php
                            $previousRecord = null;
                            $previousRecordGroupKey = null;
                            $previousRecordGroupTitle = null;
                        @endphp

                        @foreach ($records as $record)
                            @php
                                $recordAction = $getRecordAction($record);
                                $recordKey = $getRecordKey($record);
                                $recordUrl = $getRecordUrl($record);
                                $recordGroupKey = $group?->getStringKey($record);
                                $recordGroupTitle = $group?->getTitle($record);

                                $collapsibleColumnsLayout?->record($record);
                                $hasCollapsibleColumnsLayout = (bool) $collapsibleColumnsLayout?->isVisible();
                            @endphp

                            @if ($recordGroupTitle !== $previousRecordGroupTitle)
                                @if ($hasSummary && (! $isReordering) && filled($previousRecordGroupTitle))
                                    <x-filament-tables::table
                                        class="col-span-full"
                                    >
                                        <x-filament-tables::summary.row
                                            :columns="$columns"
                                            extra-heading-column
                                            :heading="
                                                __('filament-tables::table.summary.subheadings.group', [
                                                    'group' => $previousRecordGroupTitle,
                                                    'label' => $pluralModelLabel,
                                                ])
                                            "
                                            :placeholder-columns="false"
                                            :query="$group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord)"
                                            :selected-state="$groupedSummarySelectedState[$previousRecordGroupKey] ?? []"
                                        />
                                    </x-filament-tables::table>
                                @endif

                                <x-filament-tables::group.header
                                    :collapsible="$group->isCollapsible()"
                                    :description="$group->getDescription($record, $recordGroupTitle)"
                                    :label="$group->isTitlePrefixedWithLabel() ? $group->getLabel() : null"
                                    :title="$recordGroupTitle"
                                    @class([
                                        'col-span-full',
                                        '-mx-4 w-[calc(100%+2rem)] border-y border-gray-200 first:border-t-0 dark:border-white/5 sm:-mx-6 sm:w-[calc(100%+3rem)]' => $contentGrid,
                                    ])
                                    :x-bind:class="$hasSummary ? null : '{ \'-mb-4 border-b-0\': isGroupCollapsed(\'' . $recordGroupTitle . '\') }'"
                                >
                                    @if ($isSelectionEnabled)
                                        <x-slot name="start">
                                            <div class="px-3">
                                                <x-filament-tables::selection.group-checkbox
                                                    :key="$recordGroupKey"
                                                    :title="$recordGroupTitle"
                                                />
                                            </div>
                                        </x-slot>
                                    @endif
                                </x-filament-tables::group.header>
                            @endif

                            <div
                                @if ($hasCollapsibleColumnsLayout)
                                    x-data="{ isCollapsed: @js($collapsibleColumnsLayout->isCollapsed()) }"
                                    x-init="$dispatch('collapsible-table-row-initialized')"
                                    x-on:collapse-all-table-rows.window="isCollapsed = true"
                                    x-on:expand-all-table-rows.window="isCollapsed = false"
                                    x-bind:class="isCollapsed && 'fi-collapsed'"
                                @endif
                                wire:key="{{ $this->getId() }}.table.records.{{ $recordKey }}"
                                @if ($isReordering)
                                    x-sortable-item="{{ $recordKey }}"
                                    x-sortable-handle
                                @endif
                                @class([
                                    'fi-ta-record relative h-full bg-white transition duration-75 dark:bg-gray-900',
                                    'hover:bg-gray-50 dark:hover:bg-white/5' => ($recordUrl || $recordAction) && (! $contentGrid),
                                    'hover:bg-gray-50 dark:hover:bg-white/10 dark:hover:ring-white/20' => ($recordUrl || $recordAction) && $contentGrid,
                                    'rounded-xl shadow-sm ring-1 ring-gray-950/5' => $contentGrid,
                                    ...$getRecordClasses($record),
                                ])
                                x-bind:class="{
                                    'hidden':
                                        {{ $group?->isCollapsible() ? 'true' : 'false' }} &&
                                        isGroupCollapsed('{{ $recordGroupTitle }}'),
                                    {{ ($contentGrid ? '\'bg-gray-50 dark:bg-white/10 dark:ring-white/20\'' : '\'bg-gray-50 dark:bg-white/5 before:absolute before:start-0 before:inset-y-0 before:w-0.5 before:bg-primary-600 dark:before:bg-primary-500\'') . ': isRecordSelected(\'' . $recordKey . '\')' }},
                                    {{ $contentGrid ? '\'bg-white dark:bg-white/5 dark:ring-white/10\': ! isRecordSelected(\'' . $recordKey . '\')' : '\'\':\'\'' }},
                                }"
                            >
                                @php
                                    $hasItemBeforeRecordContent = $isReordering || ($isSelectionEnabled && $isRecordSelectable($record));
                                    $isRecordCollapsible = $hasCollapsibleColumnsLayout && (! $isReordering);
                                    $hasItemAfterRecordContent = $isRecordCollapsible;
                                    $recordHasActions = count($actions) && (! $isReordering);

                                    $recordContentHorizontalPaddingClasses = \Illuminate\Support\Arr::toCssClasses([
                                        'ps-3' => (! $contentGrid) && $hasItemBeforeRecordContent,
                                        'ps-4 sm:ps-6' => (! $contentGrid) && (! $hasItemBeforeRecordContent),
                                        'pe-3' => (! $contentGrid) && $hasItemAfterRecordContent,
                                        'pe-4 sm:pe-6 md:pe-3' => (! $contentGrid) && (! $hasItemAfterRecordContent),
                                        'ps-2' => $contentGrid && $hasItemBeforeRecordContent,
                                        'ps-4' => $contentGrid && (! $hasItemBeforeRecordContent),
                                        'pe-2' => $contentGrid && $hasItemAfterRecordContent,
                                        'pe-4' => $contentGrid && (! $hasItemAfterRecordContent),
                                    ]);

                                    $recordActionsClasses = \Illuminate\Support\Arr::toCssClasses([
                                        'md:ps-3' => (! $contentGrid),
                                        'ps-3' => (! $contentGrid) && $hasItemBeforeRecordContent,
                                        'ps-4 sm:ps-6' => (! $contentGrid) && (! $hasItemBeforeRecordContent),
                                        'pe-3' => (! $contentGrid) && $hasItemAfterRecordContent,
                                        'pe-4 sm:pe-6' => (! $contentGrid) && (! $hasItemAfterRecordContent),
                                        'ps-2' => $contentGrid && $hasItemBeforeRecordContent,
                                        'ps-4' => $contentGrid && (! $hasItemBeforeRecordContent),
                                        'pe-2' => $contentGrid && $hasItemAfterRecordContent,
                                        'pe-4' => $contentGrid && (! $hasItemAfterRecordContent),
                                    ]);
                                @endphp

                                <div
                                    @class([
                                        'flex items-center',
                                        'ps-1 sm:ps-3' => (! $contentGrid) && $hasItemBeforeRecordContent,
                                        'pe-1 sm:pe-3' => (! $contentGrid) && $hasItemAfterRecordContent,
                                        'ps-1' => $contentGrid && $hasItemBeforeRecordContent,
                                        'pe-1' => $contentGrid && $hasItemAfterRecordContent,
                                    ])
                                >
                                    @if ($isReordering)
                                        <x-filament-tables::reorder.handle
                                            class="mx-1 my-2"
                                        />
                                    @elseif ($isSelectionEnabled && $isRecordSelectable($record))
                                        <x-filament-tables::selection.checkbox
                                            :label="__('filament-tables::table.fields.bulk_select_record.label', ['key' => $recordKey])"
                                            :value="$recordKey"
                                            x-model="selectedRecords"
                                            :data-group="$recordGroupKey"
                                            class="fi-ta-record-checkbox mx-3 my-4"
                                        />
                                    @endif

                                    @php
                                        $recordContentClasses = \Illuminate\Support\Arr::toCssClasses([
                                            $recordContentHorizontalPaddingClasses,
                                            'block w-full',
                                        ]);
                                    @endphp

                                    <div
                                        @class([
                                            'flex w-full flex-col gap-y-3 py-4',
                                            'md:flex-row md:items-center' => ! $contentGrid,
                                        ])
                                    >
                                        <div class="flex-1">
                                            @if ($recordUrl)
                                                <a
                                                    {{ \Filament\Support\generate_href_html($recordUrl) }}
                                                    class="{{ $recordContentClasses }}"
                                                >
                                                    <x-filament-tables::columns.layout
                                                        :components="$getColumnsLayout()"
                                                        :record="$record"
                                                        :record-key="$recordKey"
                                                        :row-loop="$loop"
                                                    />
                                                </a>
                                            @elseif ($recordAction)
                                                @php
                                                    $recordWireClickAction = $getAction($recordAction)
                                                        ? "mountTableAction('{$recordAction}', '{$recordKey}')"
                                                        : $recordWireClickAction = "{$recordAction}('{$recordKey}')";
                                                @endphp

                                                <button
                                                    type="button"
                                                    wire:click="{{ $recordWireClickAction }}"
                                                    wire:loading.attr="disabled"
                                                    wire:target="{{ $recordWireClickAction }}"
                                                    class="{{ $recordContentClasses }}"
                                                >
                                                    <x-filament-tables::columns.layout
                                                        :components="$getColumnsLayout()"
                                                        :record="$record"
                                                        :record-key="$recordKey"
                                                        :row-loop="$loop"
                                                    />
                                                </button>
                                            @else
                                                <div
                                                    class="{{ $recordContentClasses }}"
                                                >
                                                    <x-filament-tables::columns.layout
                                                        :components="$getColumnsLayout()"
                                                        :record="$record"
                                                        :record-key="$recordKey"
                                                        :row-loop="$loop"
                                                    />
                                                </div>
                                            @endif

                                            @if ($hasCollapsibleColumnsLayout && (! $isReordering))
                                                <div
                                                    x-collapse
                                                    x-show="! isCollapsed"
                                                    class="{{ $recordContentHorizontalPaddingClasses }} mt-3"
                                                >
                                                    {{ $collapsibleColumnsLayout->viewData(['recordKey' => $recordKey]) }}
                                                </div>
                                            @endif
                                        </div>

                                        @if ($recordHasActions)
                                            <x-filament-tables::actions
                                                :actions="$actions"
                                                :alignment="(! $contentGrid) ? 'start md:end' : Alignment::Start"
                                                :record="$record"
                                                wrap="-sm"
                                                :class="$recordActionsClasses"
                                            />
                                        @endif
                                    </div>

                                    @if ($isRecordCollapsible)
                                        <x-filament::icon-button
                                            color="gray"
                                            icon-alias="tables::columns.collapse-button"
                                            icon="heroicon-m-chevron-down"
                                            x-on:click="isCollapsed = ! isCollapsed"
                                            class="mx-1 my-2 shrink-0"
                                            x-bind:class="{ 'rotate-180': isCollapsed }"
                                        />
                                    @endif
                                </div>
                            </div>

                            @php
                                $previousRecordGroupKey = $recordGroupKey;
                                $previousRecordGroupTitle = $recordGroupTitle;
                                $previousRecord = $record;
                            @endphp
                        @endforeach

                        @if ($hasSummary && (! $isReordering) && filled($previousRecordGroupTitle) && ((! $records instanceof \Illuminate\Contracts\Pagination\Paginator) || (! $records->hasMorePages())))
                            <x-filament-tables::table class="col-span-full">
                                <x-filament-tables::summary.row
                                    :columns="$columns"
                                    extra-heading-column
                                    :heading="__('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $pluralModelLabel])"
                                    :placeholder-columns="false"
                                    :query="$group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord)"
                                    :selected-state="$groupedSummarySelectedState[$previousRecordGroupKey] ?? []"
                                />
                            </x-filament-tables::table>
                        @endif
                    </x-filament::grid>
                @endif

                @if (($content || $hasColumnsLayout) && $contentFooter)
                    {{
                        $contentFooter->with([
                            'columns' => $columns,
                            'records' => $records,
                        ])
                    }}
                @endif

                @if ($hasSummary && (! $isReordering))
                    <x-filament-tables::table>
                        <x-filament-tables::summary
                            :columns="$columns"
                            extra-heading-column
                            :placeholder-columns="false"
                            :plural-model-label="$pluralModelLabel"
                            :records="$records"
                        />
                    </x-filament-tables::table>
                @endif
            @elseif (($records !== null) && count($records))
                <x-filament-tables::table :reorderable="$isReorderable">
                    <x-slot name="header">
                        @if ($isReordering)
                            <th></th>
                        @else
                            @if (count($actions) && $actionsPosition === ActionsPosition::BeforeCells)
                                @if ($actionsColumnLabel)
                                    <x-filament-tables::header-cell>
                                        {{ $actionsColumnLabel }}
                                    </x-filament-tables::header-cell>
                                @else
                                    <th class="w-1"></th>
                                @endif
                            @endif

                            @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
                                <x-filament-tables::selection.cell tag="th">
                                    <x-filament-tables::selection.checkbox
                                        :label="__('filament-tables::table.fields.bulk_select_page.label')"
                                        x-bind:checked="
                                            const recordsOnPage = getRecordsOnPage()

                                            if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                                                $el.checked = true

                                                return 'checked'
                                            }

                                            $el.checked = false

                                            return null
                                        "
                                        x-on:click="toggleSelectRecordsOnPage"
                                    />
                                </x-filament-tables::selection.cell>
                            @endif

                            @if (count($actions) && $actionsPosition === ActionsPosition::BeforeColumns)
                                @if ($actionsColumnLabel)
                                    <x-filament-tables::header-cell>
                                        {{ $actionsColumnLabel }}
                                    </x-filament-tables::header-cell>
                                @else
                                    <th class="w-1"></th>
                                @endif
                            @endif
                        @endif

                        @foreach ($columns as $column)
                            <x-filament-tables::header-cell
                                :actively-sorted="$getSortColumn() === $column->getName()"
                                :alignment="$column->getAlignment()"
                                :name="$column->getName()"
                                :sortable="$column->isSortable() && (! $isReordering)"
                                :sort-direction="$getSortDirection()"
                                :wrap="$column->isHeaderWrapped()"
                                :attributes="
                                    \Filament\Support\prepare_inherited_attributes($column->getExtraHeaderAttributeBag())
                                        ->class([
                                            'fi-table-header-cell-' . str($column->getName())->camel()->kebab(),
                                            $getHiddenClasses($column),
                                        ])
                                "
                            >
                                {{ $column->getLabel() }}
                            </x-filament-tables::header-cell>
                        @endforeach

                        @if (! $isReordering)
                            @if (count($actions) && $actionsPosition === ActionsPosition::AfterColumns)
                                @if ($actionsColumnLabel)
                                    <x-filament-tables::header-cell
                                        :alignment="Alignment::Right"
                                    >
                                        {{ $actionsColumnLabel }}
                                    </x-filament-tables::header-cell>
                                @else
                                    <th class="w-1"></th>
                                @endif
                            @endif

                            @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
                                <x-filament-tables::selection.cell tag="th">
                                    <x-filament-tables::selection.checkbox
                                        :label="__('filament-tables::table.fields.bulk_select_page.label')"
                                        x-bind:checked="
                                            const recordsOnPage = getRecordsOnPage()

                                            if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                                                $el.checked = true

                                                return 'checked'
                                            }

                                            $el.checked = false

                                            return null
                                        "
                                        x-on:click="toggleSelectRecordsOnPage"
                                    />
                                </x-filament-tables::selection.cell>
                            @endif

                            @if (count($actions) && $actionsPosition === ActionsPosition::AfterCells)
                                @if ($actionsColumnLabel)
                                    <x-filament-tables::header-cell
                                        :alignment="Alignment::Right"
                                    >
                                        {{ $actionsColumnLabel }}
                                    </x-filament-tables::header-cell>
                                @else
                                    <th class="w-1"></th>
                                @endif
                            @endif
                        @endif
                    </x-slot>

                    @if ($isColumnSearchVisible)
                        <x-filament-tables::row>
                            @if ($isReordering)
                                <td></td>
                            @else
                                @if (count($actions) && in_array($actionsPosition, [ActionsPosition::BeforeCells, ActionsPosition::BeforeColumns]))
                                    <td></td>
                                @endif

                                @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
                                    <td></td>
                                @endif
                            @endif

                            @foreach ($columns as $column)
                                <x-filament-tables::cell
                                    @class([
                                        'fi-table-individual-search-cell-' . str($column->getName())->camel()->kebab(),
                                        'px-3 py-2',
                                    ])
                                >
                                    @if ($column->isIndividuallySearchable())
                                        <x-filament-tables::search-field
                                            wire-model="tableColumnSearches.{{ $column->getName() }}"
                                        />
                                    @endif
                                </x-filament-tables::cell>
                            @endforeach

                            @if (! $isReordering)
                                @if (count($actions) && in_array($actionsPosition, [ActionsPosition::AfterColumns, ActionsPosition::AfterCells]))
                                    <td></td>
                                @endif

                                @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
                                    <td></td>
                                @endif
                            @endif
                        </x-filament-tables::row>
                    @endif

                    @if (($records !== null) && count($records))
                        @php
                            $isRecordRowStriped = false;
                            $previousRecord = null;
                            $previousRecordGroupKey = null;
                            $previousRecordGroupTitle = null;
                        @endphp

                        @foreach ($records as $record)
                            @php
                                $recordAction = $getRecordAction($record);
                                $recordKey = $getRecordKey($record);
                                $recordUrl = $getRecordUrl($record);
                                $recordGroupKey = $group?->getStringKey($record);
                                $recordGroupTitle = $group?->getTitle($record);
                            @endphp

                            @if ($recordGroupTitle !== $previousRecordGroupTitle)
                                @if ($hasSummary && (! $isReordering) && filled($previousRecordGroupTitle))
                                    <x-filament-tables::summary.row
                                        :actions="count($actions)"
                                        :actions-position="$actionsPosition"
                                        :columns="$columns"
                                        :groups-only="$isGroupsOnly"
                                        :heading="$isGroupsOnly ? $previousRecordGroupTitle : __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $pluralModelLabel])"
                                        :query="$group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord)"
                                        :record-checkbox-position="$recordCheckboxPosition"
                                        :selected-state="$groupedSummarySelectedState[$previousRecordGroupKey] ?? []"
                                        :selection-enabled="$isSelectionEnabled"
                                    />
                                @endif

                                @if (! $isGroupsOnly)
                                    <x-filament-tables::row>
                                        @php
                                            $groupHeaderColspan = $columnsCount;

                                            if ($isSelectionEnabled) {
                                                $groupHeaderColspan--;

                                                if (
                                                    ($recordCheckboxPosition === RecordCheckboxPosition::BeforeCells) &&
                                                    count($actions) &&
                                                    ($actionsPosition === ActionsPosition::BeforeCells)
                                                ) {
                                                    $groupHeaderColspan--;
                                                }
                                            }
                                        @endphp

                                        @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
                                            @if (count($actions) && $actionsPosition === ActionsPosition::BeforeCells)
                                                <td
                                                    class="bg-gray-50 dark:bg-white/5"
                                                ></td>
                                            @endif

                                            <x-filament-tables::selection.group-cell>
                                                <x-filament-tables::selection.group-checkbox
                                                    :key="$recordGroupKey"
                                                    :title="$recordGroupTitle"
                                                />
                                            </x-filament-tables::selection.group-cell>
                                        @endif

                                        <td
                                            colspan="{{ $groupHeaderColspan }}"
                                            class="p-0"
                                        >
                                            <x-filament-tables::group.header
                                                :collapsible="$group->isCollapsible()"
                                                :description="$group->getDescription($record, $recordGroupTitle)"
                                                :label="$group->isTitlePrefixedWithLabel() ? $group->getLabel() : null"
                                                :title="$recordGroupTitle"
                                            />
                                        </td>

                                        @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
                                            <x-filament-tables::selection.group-cell>
                                                <x-filament-tables::selection.group-checkbox
                                                    :key="$recordGroupKey"
                                                    :title="$recordGroupTitle"
                                                />
                                            </x-filament-tables::selection.group-cell>
                                        @endif
                                    </x-filament-tables::row>
                                @endif

                                @php
                                    $isRecordRowStriped = false;
                                @endphp
                            @endif

                            @if (! $isGroupsOnly)
                                <x-filament-tables::row
                                    :alpine-hidden="($group?->isCollapsible() ? 'true' : 'false') . ' && isGroupCollapsed(\'' . $recordGroupTitle . '\')'"
                                    :alpine-selected="'isRecordSelected(\'' . $recordKey . '\')'"
                                    :record-action="$recordAction"
                                    :record-url="$recordUrl"
                                    :striped="$isStriped && $isRecordRowStriped"
                                    :wire:key="$this->getId() . '.table.records.' . $recordKey"
                                    :x-sortable-handle="$isReordering"
                                    :x-sortable-item="$isReordering ? $recordKey : null"
                                    @class([
                                        'group cursor-move' => $isReordering,
                                        ...$getRecordClasses($record),
                                    ])
                                >
                                    @if ($isReordering)
                                        <x-filament-tables::reorder.cell>
                                            <x-filament-tables::reorder.handle />
                                        </x-filament-tables::reorder.cell>
                                    @endif

                                    @if (count($actions) && $actionsPosition === ActionsPosition::BeforeCells && (! $isReordering))
                                        <x-filament-tables::actions.cell>
                                            <x-filament-tables::actions
                                                :actions="$actions"
                                                :alignment="$actionsAlignment"
                                                :record="$record"
                                            />
                                        </x-filament-tables::actions.cell>
                                    @endif

                                    @if ($isSelectionEnabled && ($recordCheckboxPosition === RecordCheckboxPosition::BeforeCells) && (! $isReordering))
                                        <x-filament-tables::selection.cell>
                                            @if ($isRecordSelectable($record))
                                                <x-filament-tables::selection.checkbox
                                                    :label="__('filament-tables::table.fields.bulk_select_record.label', ['key' => $recordKey])"
                                                    :value="$recordKey"
                                                    x-model="selectedRecords"
                                                    :data-group="$recordGroupKey"
                                                    class="fi-ta-record-checkbox"
                                                />
                                            @endif
                                        </x-filament-tables::selection.cell>
                                    @endif

                                    @if (count($actions) && $actionsPosition === ActionsPosition::BeforeColumns && (! $isReordering))
                                        <x-filament-tables::actions.cell>
                                            <x-filament-tables::actions
                                                :actions="$actions"
                                                :alignment="$actionsAlignment"
                                                :record="$record"
                                            />
                                        </x-filament-tables::actions.cell>
                                    @endif

                                    @foreach ($columns as $column)
                                        @php
                                            $column->record($record);
                                            $column->rowLoop($loop->parent);
                                        @endphp

                                        <x-filament-tables::cell
                                            :wire:key="$this->getId() . '.table.record.' . $recordKey . '.column.' . $column->getName()"
                                            :attributes="
                                                \Filament\Support\prepare_inherited_attributes($column->getExtraCellAttributeBag())
                                                    ->class([
                                                        'fi-table-cell-' . str($column->getName())->camel()->kebab(),
                                                        $getHiddenClasses($column),
                                                    ])
                                            "
                                        >
                                            <x-filament-tables::columns.column
                                                :column="$column"
                                                :is-click-disabled="$column->isClickDisabled() || $isReordering"
                                                :record="$record"
                                                :record-action="$recordAction"
                                                :record-key="$recordKey"
                                                :record-url="$recordUrl"
                                            />
                                        </x-filament-tables::cell>
                                    @endforeach

                                    @if (count($actions) && $actionsPosition === ActionsPosition::AfterColumns && (! $isReordering))
                                        <x-filament-tables::actions.cell>
                                            <x-filament-tables::actions
                                                :actions="$actions"
                                                :alignment="$actionsAlignment ?? Alignment::End"
                                                :record="$record"
                                            />
                                        </x-filament-tables::actions.cell>
                                    @endif

                                    @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells && (! $isReordering))
                                        <x-filament-tables::selection.cell>
                                            @if ($isRecordSelectable($record))
                                                <x-filament-tables::selection.checkbox
                                                    :label="__('filament-tables::table.fields.bulk_select_record.label', ['key' => $recordKey])"
                                                    :value="$recordKey"
                                                    x-model="selectedRecords"
                                                    :data-group="$recordGroupKey"
                                                    class="fi-ta-record-checkbox"
                                                />
                                            @endif
                                        </x-filament-tables::selection.cell>
                                    @endif

                                    @if (count($actions) && $actionsPosition === ActionsPosition::AfterCells)
                                        <x-filament-tables::actions.cell
                                            @class([
                                                'hidden' => $isReordering,
                                            ])
                                        >
                                            <x-filament-tables::actions
                                                :actions="$actions"
                                                :alignment="$actionsAlignment ?? Alignment::End"
                                                :record="$record"
                                            />
                                        </x-filament-tables::actions.cell>
                                    @endif
                                </x-filament-tables::row>
                            @endif

                            @php
                                $isRecordRowStriped = ! $isRecordRowStriped;
                                $previousRecord = $record;
                                $previousRecordGroupKey = $recordGroupKey;
                                $previousRecordGroupTitle = $recordGroupTitle;
                            @endphp
                        @endforeach

                        @if ($hasSummary && (! $isReordering) && filled($previousRecordGroupTitle) && ((! $records instanceof \Illuminate\Contracts\Pagination\Paginator) || (! $records->hasMorePages())))
                            <x-filament-tables::summary.row
                                :actions="count($actions)"
                                :actions-position="$actionsPosition"
                                :columns="$columns"
                                :groups-only="$isGroupsOnly"
                                :heading="$isGroupsOnly ? $previousRecordGroupTitle : __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $pluralModelLabel])"
                                :query="$group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord)"
                                :record-checkbox-position="$recordCheckboxPosition"
                                :selected-state="$groupedSummarySelectedState[$previousRecordGroupKey] ?? []"
                                :selection-enabled="$isSelectionEnabled"
                            />
                        @endif

                        @if ($hasSummary && (! $isReordering))
                            <x-filament-tables::summary
                                :actions="count($actions)"
                                :actions-position="$actionsPosition"
                                :columns="$columns"
                                :groups-only="$isGroupsOnly"
                                :plural-model-label="$pluralModelLabel"
                                :record-checkbox-position="$recordCheckboxPosition"
                                :records="$records"
                                :selection-enabled="$isSelectionEnabled"
                            />
                        @endif

                        @if ($contentFooter)
                            <x-slot name="footer">
                                {{
                                    $contentFooter->with([
                                        'columns' => $columns,
                                        'records' => $records,
                                    ])
                                }}
                            </x-slot>
                        @endif
                    @endif
                </x-filament-tables::table>
            @elseif ($records === null)
                <div class="h-32"></div>
            @elseif ($emptyState = $getEmptyState())
                {{ $emptyState }}
            @else
                <tr>
                    <td colspan="{{ $columnsCount }}">
                        <x-filament-tables::empty-state
                            :actions="$getEmptyStateActions()"
                            :description="$getEmptyStateDescription()"
                            :heading="$getEmptyStateHeading()"
                            :icon="$getEmptyStateIcon()"
                        />
                    </td>
                </tr>
            @endif
        </div>

        @if ($records instanceof \Illuminate\Contracts\Pagination\Paginator && ((! ($records instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)) || $records->total()))
            <x-filament::pagination
                :page-options="$getPaginationPageOptions()"
                :paginator="$records"
                class="px-3 py-3 sm:px-6"
            />
        @endif

        @if ($hasFiltersBelowContent)
            <x-filament-tables::filters
                :form="$getFiltersForm()"
                class="p-4 sm:px-6"
            />
        @endif
    </x-filament-tables::container>

    <x-filament-actions::modals />
</div>
