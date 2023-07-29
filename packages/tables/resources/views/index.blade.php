@php
    use Filament\Tables\Actions\Position as ActionsPosition;
    use Filament\Tables\Actions\RecordCheckboxPosition;
    use Filament\Tables\Filters\Layout as FiltersLayout;

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
    $hasFiltersDropdown = $hasFilters && ($filtersLayout === FiltersLayout::Dropdown);
    $hasFiltersAboveContent = $hasFilters && in_array($filtersLayout, [FiltersLayout::AboveContent, FiltersLayout::AboveContentCollapsible]);
    $hasFiltersAboveContentCollapsible = $hasFilters && ($filtersLayout === FiltersLayout::AboveContentCollapsible);
    $hasFiltersBelowContent = $hasFilters && ($filtersLayout === FiltersLayout::BelowContent);
    $hasColumnToggleDropdown = $hasToggleableColumns();
    $hasHeader = $header || $heading || $description || ($headerActions && (! $isReordering)) || $isReorderable || count($groups) || $isGlobalSearchVisible || $hasFilters || $hasColumnToggleDropdown;
    $hasHeaderToolbar = $isReorderable || count($groups) || $isGlobalSearchVisible || $hasFiltersDropdown || $hasColumnToggleDropdown;
    $pluralModelLabel = $getPluralModelLabel();
    $records = $isLoaded ? $getRecords() : null;
    $allSelectableRecordsCount = $isLoaded ? $getAllSelectableRecordsCount() : null;
    $columnsCount = count($columns);
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
    x-data="{
        collapsedGroups: [],

        hasHeader: true,

        isLoading: false,

        selectedRecords: [],

        shouldCheckUniqueSelection: true,

        init: function () {
            $el.addEventListener('deselectAllTableRecords', () =>
                this.deselectAllRecords(),
            )

            $watch('selectedRecords', () => {
                if (! this.shouldCheckUniqueSelection) {
                    this.shouldCheckUniqueSelection = true

                    return
                }

                this.selectedRecords = [...new Set(this.selectedRecords)]

                this.shouldCheckUniqueSelection = false
            })
        },

        mountBulkAction: function (name) {
            $wire.mountTableBulkAction(name, this.selectedRecords)
        },

        toggleSelectRecordsOnPage: function () {
            let keys = this.getRecordsOnPage()

            if (this.areRecordsSelected(keys)) {
                this.deselectRecords(keys)

                return
            }

            this.selectRecords(keys)
        },

        getRecordsOnPage: function () {
            let keys = []

            for (checkbox of $el.getElementsByClassName('fi-ta-record-checkbox')) {
                keys.push(checkbox.value)
            }

            return keys
        },

        selectRecords: function (keys) {
            for (key of keys) {
                if (this.isRecordSelected(key)) {
                    continue
                }

                this.selectedRecords.push(key)
            }
        },

        deselectRecords: function (keys) {
            for (key of keys) {
                let index = this.selectedRecords.indexOf(key)

                if (index === -1) {
                    continue
                }

                this.selectedRecords.splice(index, 1)
            }
        },

        selectAllRecords: async function () {
            this.isLoading = true

            this.selectedRecords = await $wire.getAllSelectableTableRecordKeys()

            this.isLoading = false
        },

        deselectAllRecords: function () {
            this.selectedRecords = []
        },

        isRecordSelected: function (key) {
            return this.selectedRecords.includes(key)
        },

        areRecordsSelected: function (keys) {
            return keys.every((key) => this.isRecordSelected(key))
        },

        toggleCollapseGroup: function (group) {
            if (this.isGroupCollapsed(group)) {
                this.collapsedGroups.splice(this.collapsedGroups.indexOf(group), 1)

                return
            }

            this.collapsedGroups.push(group)
        },

        isGroupCollapsed: function (group) {
            return this.collapsedGroups.includes(group)
        },

        resetCollapsedGroups: function () {
            this.collapsedGroups = []
        },
    }"
    class="fi-ta"
>
    <x-filament-tables::container>
        <div
            @if (! $hasHeader) x-cloak @endif
            x-show="hasHeader = @js($hasHeader) || (selectedRecords.length && @js(count($bulkActions)))"
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
                        'grid px-4 sm:px-6',
                        'py-3 sm:py-4' => ! $hasFiltersAboveContentCollapsible,
                        'gap-y-3 py-2.5 sm:gap-y-1 sm:py-3' => $hasFiltersAboveContentCollapsible,
                    ])
                >
                    @if ($hasFiltersAboveContentCollapsible)
                        <div class="flex w-full justify-end">
                            <span
                                x-on:click="areFiltersOpen = ! areFiltersOpen"
                                @class([
                                    '-mx-2' => $filtersTriggerAction->isIconButton(),
                                ])
                            >
                                {{ $filtersTriggerAction->badge(count(\Illuminate\Support\Arr::flatten($filterIndicators))) }}
                            </span>
                        </div>
                    @endif

                    <div
                        x-show="areFiltersOpen"
                        @class([
                            'py-1 sm:py-3' => $hasFiltersAboveContentCollapsible,
                        ])
                    >
                        <x-filament-tables::filters
                            :form="$getFiltersForm()"
                        />
                    </div>
                </div>
            @endif

            <div
                @if (! $hasHeaderToolbar) x-cloak @endif
                x-show="@js($hasHeaderToolbar) || (selectedRecords.length && @js(count($bulkActions)))"
                class="fi-ta-header-toolbar flex items-center justify-between px-4 py-3 sm:px-6 gap-3"
            >
                <div class="flex shrink-0 items-center gap-x-3">
                    @if ($isReorderable)
                        @php
                            $reorderRecordsTriggerAction = $getReorderRecordsTriggerAction($isReordering)
                        @endphp

                        <span
                            @class([
                                '-mx-2' => $reorderRecordsTriggerAction->isIconButton(),
                            ])
                        >
                            {{ $reorderRecordsTriggerAction }}
                        </span>
                    @endif

                    @if (count($groups))
                        <x-filament-tables::groups
                            :dropdown-on-desktop="$areGroupsInDropdownOnDesktop()"
                            :groups="$groups"
                            :trigger-action="$getGroupRecordsTriggerAction()"
                        />
                    @endif

                    @if ((! $isReordering) && count($bulkActions))
                        <x-filament-tables::actions
                            :actions="$bulkActions"
                            x-cloak="x-cloak"
                            x-show="selectedRecords.length"
                        />
                    @endif
                </div>

                @if ($isGlobalSearchVisible || $hasFiltersDropdown || $hasColumnToggleDropdown)
                    <div
                        @class([
                            'ms-auto flex items-center',
                            'gap-x-3' => ! ($filtersTriggerAction->isIconButton() && $toggleColumnsTriggerAction->isIconButton()),
                            'gap-x-4' => $filtersTriggerAction->isIconButton() && $toggleColumnsTriggerAction->isIconButton(),
                        ])
                    >
                        @if ($isGlobalSearchVisible)
                            <x-filament-tables::search-field />
                        @endif

                        @if ($hasFiltersDropdown || $hasColumnToggleDropdown)
                            @if ($hasFiltersDropdown)
                                <x-filament-tables::filters.dropdown
                                    :form="$getFiltersForm()"
                                    :indicators-count="count(\Illuminate\Support\Arr::flatten($filterIndicators))"
                                    :max-height="$getFiltersFormMaxHeight()"
                                    :trigger-action="$filtersTriggerAction"
                                    :width="$getFiltersFormWidth()"
                                />
                            @endif

                            @if ($hasColumnToggleDropdown)
                                <x-filament-tables::column-toggle.dropdown
                                    :form="$getColumnToggleForm()"
                                    :max-height="$getColumnToggleFormMaxHeight()"
                                    :trigger-action="$toggleColumnsTriggerAction"
                                    :width="$getColumnToggleFormWidth()"
                                />
                            @endif
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- TODO: review from here --}}

        @if ($isReordering)
            <x-filament-tables::reorder.indicator :colspan="$columnsCount" />
        @elseif ($isSelectionEnabled && $isLoaded)
            <x-filament-tables::selection.indicator
                :all-selectable-records-count="$allSelectableRecordsCount"
                :colspan="$columnsCount"
                x-show="selectedRecords.length"
            />
        @endif

        <x-filament-tables::filters.indicators
            :indicators="$filterIndicators"
        />

        <div
            @if ($pollingInterval = $getPollingInterval())
                wire:poll.{{ $pollingInterval }}
            @endif
            @class([
                'fi-ta-content overflow-x-auto',
                'overflow-x-auto' => $content || $hasColumnsLayout,
                'rounded-t-xl' => ! $hasHeader,
            ])
            x-bind:class="{ 'rounded-t-xl': ! hasHeader }"
        >
            @if (($content || $hasColumnsLayout) && ($records !== null) && count($records))
                @if (! $isReordering)
                    @php
                        $sortableColumns = array_filter(
                            $columns,
                            fn (\Filament\Tables\Columns\Column $column): bool => $column->isSortable(),
                        );
                    @endphp

                    <div
                        @class([
                            'flex items-center gap-4 bg-gray-50 px-3 dark:bg-white/5 sm:px-6',
                            'hidden' => (! $isSelectionEnabled) && (! count($sortableColumns)),
                        ])
                    >
                        @if ($isSelectionEnabled)
                            <x-filament-tables::selection.checkbox
                                :label="__('filament-tables::table.fields.bulk_select_page.label')"
                                x-bind:checked="
                                    let recordsOnPage = getRecordsOnPage()

                                    if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                                        $el.checked = true

                                        return 'checked'
                                    }

                                    $el.checked = false

                                    return null
                                "
                                x-on:click="toggleSelectRecordsOnPage"
                                @class(['hidden' => $isReordering])
                            />
                        @endif

                        @if (count($sortableColumns))
                            <div
                                x-data="{
                                    column: $wire.entangle('tableSortColumn').live,
                                    direction: $wire.entangle('tableSortDirection').live,
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
                                class="flex flex-wrap items-center gap-1 py-1 text-xs sm:text-sm"
                            >
                                <label>
                                    <span class="me-1 font-medium">
                                        {{ __('filament-tables::table.sorting.fields.column.label') }}
                                    </span>

                                    <x-filament-forms::affixes>
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
                                    </x-filament-forms::affixes>
                                </label>

                                <label x-cloak x-show="column">
                                    <span class="sr-only">
                                        {{ __('filament-tables::table.sorting.fields.direction.label') }}
                                    </span>

                                    <x-filament-forms::affixes
                                        x-model="direction"
                                    >
                                        <x-filament::input.select>
                                            <option value="asc">
                                                {{ __('filament-tables::table.sorting.fields.direction.options.asc') }}
                                            </option>

                                            <option value="desc">
                                                {{ __('filament-tables::table.sorting.fields.direction.options.desc') }}
                                            </option>
                                        </x-filament::input.select>
                                    </x-filament-forms::affixes>
                                </label>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($content)
                    {{ $content->with(['records' => $records]) }}
                @else
                    <x-filament::grid
                        x-sortable
                        x-on:end.stop="$wire.reorderTable($event.target.sortable.toArray())"
                        :default="$contentGrid['default'] ?? 1"
                        :sm="$contentGrid['sm'] ?? null"
                        :md="$contentGrid['md'] ?? null"
                        :lg="$contentGrid['lg'] ?? null"
                        :xl="$contentGrid['xl'] ?? null"
                        :two-xl="$contentGrid['2xl'] ?? null"
                        @class([
                            'p-2 gap-2' => $contentGrid,
                            'divide-y divide-gray-200 dark:divide-white/5' => ! $contentGrid,
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
                                $recordGroupKey = $group?->getKey($record);
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
                                            :heading="__('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $pluralModelLabel])"
                                            :query="$group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord)"
                                            :selected-state="$groupedSummarySelectedState[$previousRecordGroupKey] ?? []"
                                            extra-heading-column
                                            :placeholder-columns="false"
                                        />
                                    </x-filament-tables::table>
                                @endif

                                <div
                                    @class([
                                        'col-span-full',
                                        'rounded-xl shadow-sm' => $contentGrid,
                                    ])
                                >
                                    @php
                                        $tag = $group->isCollapsible() ? 'button' : 'div';
                                    @endphp

                                    <x-filament-tables::group.header
                                        :collapsible="$group->isCollapsible()"
                                        :description="$group->getDescription($record, $recordGroupTitle)"
                                        :label="$group->isTitlePrefixedWithLabel() ? $group->getLabel() : null"
                                        :title="$recordGroupTitle"
                                    />
                                </div>
                            @endif

                            <div
                                @if ($hasCollapsibleColumnsLayout)
                                    x-data="{ isCollapsed: @js($collapsibleColumnsLayout->isCollapsed()) }"
                                    x-init="$dispatch('collapsible-table-row-initialized')"
                                    x-on:expand-all-table-rows.window="isCollapsed = false"
                                    x-on:collapse-all-table-rows.window="isCollapsed = true"
                                @endif
                                wire:key="{{ $this->getId() }}.table.records.{{ $recordKey }}"
                                @if ($isReordering)
                                    x-sortable-item="{{ $recordKey }}"
                                    x-sortable-handle
                                @endif
                                x-bind:class="{
                                    'hidden':
                                        {{ $group?->isCollapsible() ? 'true' : 'false' }} &&
                                        isGroupCollapsed('{{ $recordGroupTitle }}'),
                                }"
                            >
                                <div
                                    x-bind:class="{
                                        'bg-gray-50 dark:bg-gray-500/10': isRecordSelected('{{ $recordKey }}'),
                                    }"
                                    @class([
                                        'relative h-full px-3 transition sm:px-6',
                                        'hover:bg-gray-50 dark:hover:bg-gray-500/10' => $recordUrl || $recordAction,
                                        'group' => $isReordering,
                                        'rounded-xl shadow-sm dark:bg-gray-700/40' => $contentGrid,
                                        ...$getRecordClasses($record),
                                    ])
                                >
                                    <div
                                        @class([
                                            'items-center gap-4 md:me-0 md:flex' => (! $contentGrid),
                                            'me-6' => $isSelectionEnabled || $hasCollapsibleColumnsLayout || $isReordering,
                                        ])
                                    >
                                        <x-filament-tables::reorder.handle
                                            @class([
                                                'absolute top-3 end-3',
                                                'md:relative md:top-0 end-0' => ! $contentGrid,
                                                'hidden' => ! $isReordering,
                                            ])
                                        />

                                        @if ($isSelectionEnabled && $isRecordSelectable($record))
                                            <x-filament-tables::selection.checkbox
                                                :label="__('filament-tables::table.fields.bulk_select_record.label', ['key' => $recordKey])"
                                                :value="$recordKey"
                                                x-model="selectedRecords"
                                                @class([
                                                    'fi-ta-record-checkbox absolute top-3 end-3',
                                                    'md:relative md:top-0 md:end-0' => ! $contentGrid,
                                                    'hidden' => $isReordering,
                                                ])
                                            />
                                        @endif

                                        @if ($hasCollapsibleColumnsLayout)
                                            <div
                                                @class([
                                                    'absolute end-1',
                                                    'top-10' => $isSelectionEnabled,
                                                    'top-1' => ! $isSelectionEnabled,
                                                    'md:relative md:end-0 md:top-0' => ! $contentGrid,
                                                    'hidden' => $isReordering,
                                                ])
                                            >
                                                <x-filament::icon-button
                                                    icon="heroicon-m-chevron-down"
                                                    icon-alias="tables::columns.collapse-button"
                                                    color="gray"
                                                    size="sm"
                                                    x-on:click="isCollapsed = ! isCollapsed"
                                                    x-bind:class="isCollapsed || '-rotate-180'"
                                                    class="transition"
                                                />
                                            </div>
                                        @endif

                                        @if ($recordUrl)
                                            <a
                                                href="{{ $recordUrl }}"
                                                class="fi-ta-record-url-link block flex-1 py-4"
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
                                                if ($getAction($recordAction)) {
                                                    $recordWireClickAction = "mountTableAction('{$recordAction}', '{$recordKey}')";
                                                } else {
                                                    $recordWireClickAction = "{$recordAction}('{$recordKey}')";
                                                }
                                            @endphp

                                            <button
                                                wire:click="{{ $recordWireClickAction }}"
                                                wire:target="{{ $recordWireClickAction }}"
                                                wire:loading.attr="disabled"
                                                type="button"
                                                class="fi-ta-record-action-btn block flex-1 py-3 disabled:pointer-events-none disabled:opacity-70"
                                            >
                                                <x-filament-tables::columns.layout
                                                    :components="$getColumnsLayout()"
                                                    :record="$record"
                                                    :record-key="$recordKey"
                                                    :row-loop="$loop"
                                                />
                                            </button>
                                        @else
                                            <div class="flex-1 py-3">
                                                <x-filament-tables::columns.layout
                                                    :components="$getColumnsLayout()"
                                                    :record="$record"
                                                    :record-key="$recordKey"
                                                    :row-loop="$loop"
                                                />
                                            </div>
                                        @endif

                                        @if (count($actions))
                                            <x-filament-tables::actions
                                                :actions="$actions"
                                                :alignment="$actionsPosition === ActionsPosition::AfterContent ? 'start' : 'start sm:end'"
                                                :record="$record"
                                                wrap="-sm"
                                                @class([
                                                    'absolute bottom-1 end-1' => $actionsPosition === ActionsPosition::BottomCorner,
                                                    'md:relative md:bottom-0 md:end-0' => $actionsPosition === ActionsPosition::BottomCorner && (! $contentGrid),
                                                    'mb-3' => $actionsPosition === ActionsPosition::AfterContent,
                                                    'md:mb-0' => $actionsPosition === ActionsPosition::AfterContent && (! $contentGrid),
                                                    'hidden' => $isReordering,
                                                ])
                                            />
                                        @endif
                                    </div>

                                    @if ($hasCollapsibleColumnsLayout)
                                        <div
                                            x-show="! isCollapsed"
                                            x-collapse
                                            @class([
                                                '-mx-2 pb-2',
                                                'md:ps-20' => (! $contentGrid) && $isSelectionEnabled,
                                                'md:ps-12' => (! $contentGrid) && (! $isSelectionEnabled),
                                                'hidden' => $isReordering,
                                            ])
                                        >
                                            {{ $collapsibleColumnsLayout->viewData(['recordKey' => $recordKey]) }}
                                        </div>
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
                                    :heading="__('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $pluralModelLabel])"
                                    :query="$group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord)"
                                    :selected-state="$groupedSummarySelectedState[$previousRecordGroupKey] ?? []"
                                    extra-heading-column
                                    :placeholder-columns="false"
                                />
                            </x-filament-tables::table>
                        @endif
                    </x-filament::grid>
                @endif

                @if (($content || $hasColumnsLayout) && $contentFooter)
                    {{ $contentFooter->with(['columns' => $columns, 'records' => $records]) }}
                @endif

                @if ($hasSummary && (! $isReordering))
                    <x-filament-tables::table>
                        <x-filament-tables::summary
                            :columns="$columns"
                            :plural-model-label="$pluralModelLabel"
                            :records="$records"
                            extra-heading-column
                            :placeholder-columns="false"
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
                                    <th class="w-5"></th>
                                @endif
                            @endif

                            @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
                                <x-filament-tables::selection.cell tag="th">
                                    <x-filament-tables::selection.checkbox
                                        :label="__('filament-tables::table.fields.bulk_select_page.label')"
                                        x-bind:checked="
                                            let recordsOnPage = getRecordsOnPage()

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
                                    <th class="w-5"></th>
                                @endif
                            @endif
                        @endif

                        @if ($isGroupsOnly)
                            <th
                                class="fi-ta-header-cell whitespace-nowrap px-4 py-2 text-sm font-medium text-gray-950 dark:text-white"
                            >
                                {{ $group->getLabel() }}
                            </th>
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
                                        alignment="right"
                                    >
                                        {{ $actionsColumnLabel }}
                                    </x-filament-tables::header-cell>
                                @else
                                    <th class="w-5"></th>
                                @endif
                            @endif

                            @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
                                <x-filament-tables::selection.cell tag="th">
                                    <x-filament-tables::selection.checkbox
                                        :label="__('filament-tables::table.fields.bulk_select_page.label')"
                                        x-bind:checked="
                                            let recordsOnPage = getRecordsOnPage()

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
                                        alignment="right"
                                    >
                                        {{ $actionsColumnLabel }}
                                    </x-filament-tables::header-cell>
                                @else
                                    <th class="w-5"></th>
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
                                        'px-3 py-4',
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
                                $recordGroupKey = $group?->getKey($record);
                                $recordGroupTitle = $group?->getTitle($record);
                            @endphp

                            @if ($recordGroupTitle !== $previousRecordGroupTitle)
                                @if ($hasSummary && (! $isReordering) && filled($previousRecordGroupTitle))
                                    <x-filament-tables::summary.row
                                        :actions="count($actions)"
                                        :actions-position="$actionsPosition"
                                        :columns="$columns"
                                        :heading="$isGroupsOnly ? $previousRecordGroupTitle : __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $pluralModelLabel])"
                                        :groups-only="$isGroupsOnly"
                                        :selection-enabled="$isSelectionEnabled"
                                        :query="$group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord)"
                                        :selected-state="$groupedSummarySelectedState[$previousRecordGroupKey] ?? []"
                                        :record-checkbox-position="$recordCheckboxPosition"
                                    />
                                @endif

                                @if (! $isGroupsOnly)
                                    <x-filament-tables::row>
                                        <td
                                            colspan="{{ $columnsCount }}"
                                            class="p-0"
                                        >
                                            <x-filament-tables::group.header
                                                :collapsible="$group->isCollapsible()"
                                                :description="$group->getDescription($record, $recordGroupTitle)"
                                                :label="$group->isTitlePrefixedWithLabel() ? $group->getLabel() : null"
                                                :title="$recordGroupTitle"
                                            />
                                        </td>
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
                                    :x-sortable-item="$isReordering ? $recordKey : null"
                                    :x-sortable-handle="$isReordering"
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

                                    @if (count($actions) && $actionsPosition === ActionsPosition::BeforeCells)
                                        <x-filament-tables::actions.cell
                                            @class([
                                                'hidden' => $isReordering,
                                            ])
                                        >
                                            <x-filament-tables::actions
                                                :actions="$actions"
                                                :alignment="$actionsAlignment ?? 'start'"
                                                :record="$record"
                                            />
                                        </x-filament-tables::actions.cell>
                                    @endif

                                    @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
                                        <x-filament-tables::selection.cell
                                            @class([
                                                'hidden' => $isReordering,
                                            ])
                                            :attributes="
                                                \Filament\Support\prepare_inherited_attributes(
                                                    new \Illuminate\View\ComponentAttributeBag([
                                                        'x-bind:class' => '{ \'relative before:absolute before:start-0 before:inset-y-0 before:w-0.5 before:bg-primary-600 dark:before:bg-primary-500\': isRecordSelected(\'' . $recordKey . '\') }',
                                                    ])
                                                )
                                            "
                                        >
                                            @if ($isRecordSelectable($record))
                                                <x-filament-tables::selection.checkbox
                                                    :label="__('filament-tables::table.fields.bulk_select_record.label', ['key' => $recordKey])"
                                                    :value="$recordKey"
                                                    x-model="selectedRecords"
                                                    class="fi-ta-record-checkbox"
                                                />
                                            @endif
                                        </x-filament-tables::selection.cell>
                                    @endif

                                    @if (count($actions) && $actionsPosition === ActionsPosition::BeforeColumns)
                                        <x-filament-tables::actions.cell
                                            @class([
                                                'hidden' => $isReordering,
                                            ])
                                        >
                                            <x-filament-tables::actions
                                                :actions="$actions"
                                                :alignment="$actionsAlignment ?? 'start'"
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
                                            :attributes="
                                                \Filament\Support\prepare_inherited_attributes($column->getExtraCellAttributeBag())
                                                    ->merge([
                                                        'wire:key' => $this->getId() . '.table.record.' . $recordKey . '.column.' . $column->getName(),
                                                    ])
                                                    ->class([
                                                        'fi-table-cell-' . str($column->getName())->camel()->kebab(),
                                                        $getHiddenClasses($column),
                                                    ])
                                            "
                                        >
                                            <x-filament-tables::columns.column
                                                :column="$column"
                                                :record="$record"
                                                :record-action="$recordAction"
                                                :record-key="$recordKey"
                                                :record-url="$recordUrl"
                                                :is-click-disabled="$column->isClickDisabled() || $isReordering"
                                            />
                                        </x-filament-tables::cell>
                                    @endforeach

                                    @if (count($actions) && $actionsPosition === ActionsPosition::AfterColumns)
                                        <x-filament-tables::actions.cell
                                            @class([
                                                'hidden' => $isReordering,
                                            ])
                                        >
                                            <x-filament-tables::actions
                                                :actions="$actions"
                                                :alignment="$actionsAlignment ?? 'end'"
                                                :record="$record"
                                            />
                                        </x-filament-tables::actions.cell>
                                    @endif

                                    @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
                                        <x-filament-tables::selection.cell
                                            @class([
                                                'hidden' => $isReordering,
                                            ])
                                        >
                                            @if ($isRecordSelectable($record))
                                                <x-filament-tables::selection.checkbox
                                                    :label="__('filament-tables::table.fields.bulk_select_record.label', ['key' => $recordKey])"
                                                    :value="$recordKey"
                                                    x-model="selectedRecords"
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
                                                :alignment="$actionsAlignment ?? 'end'"
                                                :record="$record"
                                            />
                                        </x-filament-tables::actions.cell>
                                    @endif
                                </x-filament-tables::row>
                            @endif

                            @php
                                $isRecordRowStriped = ! $isRecordRowStriped;
                                $previousRecordGroupKey = $recordGroupKey;
                                $previousRecordGroupTitle = $recordGroupTitle;
                                $previousRecord = $record;
                            @endphp
                        @endforeach

                        @if ($hasSummary && (! $isReordering) && filled($previousRecordGroupTitle) && ((! $records instanceof \Illuminate\Contracts\Pagination\Paginator) || (! $records->hasMorePages())))
                            <x-filament-tables::summary.row
                                :actions="count($actions)"
                                :actions-position="$actionsPosition"
                                :columns="$columns"
                                :heading="$isGroupsOnly ? $previousRecordGroupTitle : __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $pluralModelLabel])"
                                :groups-only="$isGroupsOnly"
                                :selection-enabled="$isSelectionEnabled"
                                :query="$group->scopeQuery($this->getAllTableSummaryQuery(), $previousRecord)"
                                :selected-state="$groupedSummarySelectedState[$previousRecordGroupKey] ?? []"
                                :record-checkbox-position="$recordCheckboxPosition"
                            />
                        @endif

                        @if ($contentFooter)
                            <x-slot name="footer">
                                {{ $contentFooter->with(['columns' => $columns, 'records' => $records]) }}
                            </x-slot>
                        @endif

                        @if ($hasSummary && (! $isReordering))
                            <x-filament-tables::summary
                                :actions="count($actions)"
                                :actions-position="$actionsPosition"
                                :columns="$columns"
                                :groups-only="$isGroupsOnly"
                                :selection-enabled="$isSelectionEnabled"
                                :plural-model-label="$pluralModelLabel"
                                :records="$records"
                                :record-checkbox-position="$recordCheckboxPosition"
                            />
                        @endif
                    @endif
                </x-filament-tables::table>
            @elseif ($records === null)
                <div
                    class="fi-ta-defer-loading-indicator flex items-center justify-center p-6"
                >
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-full bg-primary-50 text-primary-500 dark:bg-gray-700"
                    >
                        <x-filament::loading-indicator class="h-6 w-6" />
                    </div>
                </div>
            @else
                @if ($emptyState = $getEmptyState())
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
            @endif
        </div>

        @if ($records instanceof \Illuminate\Contracts\Pagination\Paginator && ((! ($records instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)) || $records->total()))
            <div class="fi-ta-pagination-ctn px-3 py-3 sm:px-6">
                <x-filament::pagination
                    :page-options="$getPaginationPageOptions()"
                    :paginator="$records"
                />
            </div>
        @endif

        @if ($hasFiltersBelowContent)
            <div class="mt-2 p-6">
                <x-filament-tables::filters :form="$getFiltersForm()" />
            </div>
        @endif
    </x-filament-tables::container>

    <x-filament-actions::modals />
</div>
