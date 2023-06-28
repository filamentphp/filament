@php
    use Filament\Tables\Actions\Position as ActionsPosition;
    use Filament\Tables\Actions\RecordCheckboxPosition;
    use Filament\Tables\Filters\Layout as FiltersLayout;

    $actions = $getActions();
    $actionsPosition = $getActionsPosition();
    $actionsColumnLabel = $getActionsColumnLabel();
    $columns = $getColumns();
    $collapsibleColumnsLayout = $getCollapsibleColumnsLayout();
    $content = $getContent();
    $contentGrid = $getContentGrid();
    $contentFooter = $getContentFooter();
    $filterIndicators = collect($getFilters())
        ->mapWithKeys(fn (\Filament\Tables\Filters\BaseFilter $filter): array => [$filter->getName() => $filter->getIndicators()])
        ->filter(fn (array $indicators): bool => count($indicators))
        ->all();
    $hasColumnsLayout = $hasColumnsLayout();
    $header = $getHeader();
    $headerActions = $getHeaderActions();
    $heading = $getHeading();
    $description = $getDescription();
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
    $hasFiltersPopover = $hasFilters && ($filtersLayout === FiltersLayout::Popover);
    $hasFiltersAboveContent = $hasFilters && in_array($filtersLayout, [FiltersLayout::AboveContent, FiltersLayout::AboveContentCollapsible]);
    $hasFiltersAboveContentCollapsible = $hasFilters && ($filtersLayout === FiltersLayout::AboveContentCollapsible);
    $hasFiltersAfterContent = $hasFilters && ($filtersLayout === FiltersLayout::BelowContent);
    $isColumnToggleFormVisible = $hasToggleableColumns();
    $records = $isLoaded ? $getRecords() : null;
    $allSelectableRecordsCount = $isLoaded ? $getAllSelectableRecordsCount() : null;
    $columnsCount = count($columns);

    if (count($actions) && (! $isReordering)) {
        $columnsCount++;
    }

    if ($isSelectionEnabled || $isReordering) {
        $columnsCount++;
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
    x-data="{
        hasHeader: true,

        isLoading: false,

        selectedRecords: [],

        shouldCheckUniqueSelection: true,

        init: function () {
            $wire.on('deselectAllTableRecords', () => this.deselectAllRecords())

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

            for (checkbox of $el.getElementsByClassName(
                'filament-tables-record-checkbox',
            )) {
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
    }"
    class="filament-tables-component"
    @if (! $isLoaded)
        wire:init="loadTable"
    @endif
>
    <x-tables::container>
        <div
            class="filament-tables-header-container"
            x-show="hasHeader = @js($renderHeader = ($header || $heading || ($headerActions && (! $isReordering)) || $isReorderable || $isGlobalSearchVisible || $hasFilters || $isColumnToggleFormVisible)) || selectedRecords.length"
            {!! ! $renderHeader ? 'x-cloak' : null !!}
        >
            @if ($header)
                {{ $header }}
            @elseif ($heading || $headerActions)
                <div
                    @class([
                        'px-2 pt-2',
                        'hidden' => ! $heading && $isReordering,
                    ])
                >
                    <x-tables::header
                        :actions="$isReordering ? [] : $headerActions"
                        class="mb-2"
                    >
                        <x-slot name="heading">
                            {{ $heading }}
                        </x-slot>

                        <x-slot name="description">
                            {{ $description }}
                        </x-slot>
                    </x-tables::header>

                    <x-tables::hr
                        :x-show="\Illuminate\Support\Js::from($isReorderable || $isGlobalSearchVisible || $hasFilters || $isColumnToggleFormVisible) . ' || selectedRecords.length'"
                    />
                </div>
            @endif

            @if ($hasFiltersAboveContent)
                <div
                    class="px-2 pt-2"
                    x-data="{ areFiltersOpen: @js(! $hasFiltersAboveContentCollapsible) }"
                >
                    @if ($hasFiltersAboveContentCollapsible)
                        <div class="flex w-full justify-end">
                            <x-tables::filters.trigger
                                x-on:click="areFiltersOpen = ! areFiltersOpen"
                            />
                        </div>
                    @endif

                    <div class="mb-2 p-4" x-show="areFiltersOpen">
                        <x-tables::filters :form="$getFiltersForm()" />
                    </div>

                    <x-tables::hr
                        :x-show="\Illuminate\Support\Js::from($isReorderable || $isGlobalSearchVisible || $isColumnToggleFormVisible) . ' || selectedRecords.length'"
                    />
                </div>
            @endif

            <div
                x-show="@js($shouldRenderHeaderDiv = ($isReorderable || $isGlobalSearchVisible || $hasFiltersPopover || $isColumnToggleFormVisible)) || selectedRecords.length"
                {!! ! $shouldRenderHeaderDiv ? 'x-cloak' : null !!}
                class="filament-tables-header-toolbar flex h-14 items-center justify-between p-2"
                x-bind:class="{
                    'gap-2': @js($isReorderable) || selectedRecords.length,
                }"
            >
                <div class="flex items-center gap-2">
                    @if ($isReorderable)
                        <x-tables::reorder.trigger :enabled="$isReordering" />
                    @endif

                    @if (! $isReordering)
                        <x-tables::bulk-actions
                            x-show="selectedRecords.length"
                            :actions="$getBulkActions()"
                        />
                    @endif
                </div>

                @if ($isGlobalSearchVisible || $hasFiltersPopover || $isColumnToggleFormVisible)
                    <div
                        class="flex w-full items-center justify-end gap-2 md:max-w-md"
                    >
                        @if ($isGlobalSearchVisible)
                            <div
                                class="filament-tables-search-container flex flex-1 items-center justify-end"
                            >
                                <x-tables::search-input />
                            </div>
                        @endif

                        @if ($hasFiltersPopover)
                            <x-tables::filters.popover
                                :form="$getFiltersForm()"
                                :max-height="$getFiltersFormMaxHeight()"
                                :width="$getFiltersFormWidth()"
                                :indicators-count="count(\Illuminate\Support\Arr::flatten($filterIndicators))"
                                class="shrink-0"
                            />
                        @endif

                        @if ($isColumnToggleFormVisible)
                            <x-tables::toggleable
                                :form="$getColumnToggleForm()"
                                :max-height="$getColumnToggleFormMaxHeight()"
                                :width="$getColumnToggleFormWidth()"
                                class="shrink-0"
                            />
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @if ($isReordering)
            <x-tables::reorder.indicator
                :colspan="$columnsCount"
                :class="
                    \Illuminate\Support\Arr::toCssClasses([
                        'border-t',
                        'dark:border-gray-700' => config('tables.dark_mode'),
                    ])
                "
            />
        @elseif ($isSelectionEnabled && $isLoaded)
            <x-tables::selection-indicator
                :all-selectable-records-count="$allSelectableRecordsCount"
                :colspan="$columnsCount"
                x-show="selectedRecords.length"
                :class="
                    \Illuminate\Support\Arr::toCssClasses([
                        'border-t',
                        'dark:border-gray-700' => config('tables.dark_mode'),
                    ])
                "
            >
                <x-slot name="selectedRecordsCount">
                    <span x-text="selectedRecords.length"></span>
                </x-slot>
            </x-tables::selection-indicator>
        @endif

        <x-tables::filters.indicators
            :indicators="$filterIndicators"
            :class="
                \Illuminate\Support\Arr::toCssClasses([
                    'border-t',
                    'dark:border-gray-700' => config('tables.dark_mode'),
                ])
            "
        />

        <div
            @if ($pollingInterval = $getPollingInterval())
                wire:poll.{{ $pollingInterval }}
            @endif
            @class([
                'filament-tables-table-container relative overflow-x-auto',
                'dark:border-gray-700' => config('tables.dark_mode'),
                'overflow-x-auto' => $content || $hasColumnsLayout,
                'rounded-t-xl' => ! $renderHeader,
                'border-t' => $renderHeader,
            ])
            x-bind:class="{
                'rounded-t-xl': ! hasHeader,
                'border-t': hasHeader,
            }"
        >
            @if (($content || $hasColumnsLayout) && ($records !== null) && count($records))
                @if (($content || $hasColumnsLayout) && (! $isReordering))
                    @php
                        $sortableColumns = array_filter(
                            $columns,
                            fn (\Filament\Tables\Columns\Column $column): bool => $column->isSortable(),
                        );
                    @endphp

                    <div
                        @class([
                            'flex items-center gap-4 border-b bg-gray-500/5 px-4',
                            'dark:border-gray-700' => config('tables.dark_mode'),
                            'hidden' => (! $isSelectionEnabled) && (! count($sortableColumns)),
                        ])
                    >
                        @if ($isSelectionEnabled)
                            <x-tables::checkbox
                                :label="__('tables::table.fields.bulk_select_page.label')"
                                x-on:click="toggleSelectRecordsOnPage"
                                x-bind:checked="
                                    let recordsOnPage = getRecordsOnPage()

                                    if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                                        $el.checked = true

                                        return 'checked'
                                    }

                                    $el.checked = false

                                    return null
                                "
                                :class="
                                    \Illuminate\Support\Arr::toCssClasses([
                                        'hidden' => $isReordering,
                                    ])
                                "
                            />
                        @endif

                        @if (count($sortableColumns))
                            <div
                                x-data="{
                                    column: $wire.entangle('tableSortColumn'),
                                    direction: $wire.entangle('tableSortDirection'),
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
                                    <span class="mr-1 font-medium">
                                        {{ __('tables::table.sorting.fields.column.label') }}
                                    </span>

                                    <select
                                        x-model="column"
                                        style="
                                            background-position: right 0.2rem
                                                center;
                                        "
                                        @class([
                                            'rounded-lg border-0 border-gray-300 bg-gray-500/5 py-1 pl-2 pr-6 text-xs font-medium focus:border-primary-500 focus:ring-0 focus:ring-primary-500 sm:text-sm',
                                            'dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500' => config('tables.dark_mode'),
                                        ])
                                    >
                                        <option value="">-</option>

                                        @foreach ($sortableColumns as $column)
                                            <option
                                                value="{{ $column->getName() }}"
                                            >
                                                {{ $column->getLabel() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </label>

                                <label>
                                    <span class="sr-only">
                                        {{ __('tables::table.sorting.fields.direction.label') }}
                                    </span>

                                    <select
                                        x-show="column"
                                        x-model="direction"
                                        style="
                                            background-position: right 0.2rem
                                                center;
                                        "
                                        @class([
                                            'rounded-lg border-0 border-gray-300 bg-gray-500/5 py-1 pl-2 pr-6 text-xs font-medium focus:border-primary-500 focus:ring-0 focus:ring-primary-500 sm:text-sm',
                                            'dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500' => config('tables.dark_mode'),
                                        ])
                                    >
                                        <option value="asc">
                                            {{ __('tables::table.sorting.fields.direction.options.asc') }}
                                        </option>

                                        <option value="desc">
                                            {{ __('tables::table.sorting.fields.direction.options.desc') }}
                                        </option>
                                    </select>
                                </label>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($content)
                    {{ $content->with(['records' => $records]) }}
                @else
                    <x-filament-support::grid
                        wire:sortable
                        wire:end.stop="reorderTable($event.target.sortable.toArray())"
                        wire:sortable.options="{ animation: 100 }"
                        :default="$contentGrid['default'] ?? 1"
                        :sm="$contentGrid['sm'] ?? null"
                        :md="$contentGrid['md'] ?? null"
                        :lg="$contentGrid['lg'] ?? null"
                        :xl="$contentGrid['xl'] ?? null"
                        :two-xl="$contentGrid['2xl'] ?? null"
                        :class="
                            \Illuminate\Support\Arr::toCssClasses([
                                'divide-y' => ! $contentGrid,
                                'p-2 gap-2' => $contentGrid,
                                'dark:divide-gray-700' => config('tables.dark_mode'),
                            ])
                        "
                    >
                        @foreach ($records as $record)
                            @php
                                $recordAction = $getRecordAction($record);
                                $recordKey = $getRecordKey($record);
                                $recordUrl = $getRecordUrl($record);

                                $collapsibleColumnsLayout?->record($record);
                                $hasCollapsibleColumnsLayout = $collapsibleColumnsLayout && (! $collapsibleColumnsLayout->isHidden());
                            @endphp

                            <div
                                @if ($hasCollapsibleColumnsLayout)
                                    x-data="{ isCollapsed: @js($collapsibleColumnsLayout->isCollapsed()) }"
                                    x-init="$dispatch('collapsible-table-row-initialized')"
                                    x-on:expand-all-table-rows.window="isCollapsed = false"
                                    x-on:collapse-all-table-rows.window="isCollapsed = true"
                                @endif
                                wire:key="{{ $this->id }}.table.records.{{ $recordKey }}"
                                @if ($isReordering)
                                    wire:sortable.item="{{ $recordKey }}"
                                    wire:sortable.handle
                                @endif
                            >
                                <div
                                    x-bind:class="{
                                        'bg-gray-50 {{ config('tables.dark_mode') ? 'dark:bg-gray-500/10' : '' }}':
                                            isRecordSelected('{{ $recordKey }}'),
                                    }"
                                    @class(array_merge(
                                        [
                                            'relative h-full px-4 transition',
                                            'hover:bg-gray-50' => $recordUrl || $recordAction,
                                            'dark:hover:bg-gray-500/10' => ($recordUrl || $recordAction) && config('tables.dark_mode'),
                                            'dark:border-gray-600' => (! $contentGrid) && config('tables.dark_mode'),
                                            'group' => $isReordering,
                                            'rounded-xl border border-gray-200 shadow-sm' => $contentGrid,
                                            'dark:border-gray-700 dark:bg-gray-700/40' => $contentGrid && config('tables.dark_mode'),
                                        ],
                                        $getRecordClasses($record),
                                    ))
                                >
                                    <div
                                        @class([
                                            'items-center gap-4 md:mr-0 md:flex rtl:md:ml-0' => (! $contentGrid),
                                            'mr-6 rtl:ml-6 rtl:mr-0' => $isSelectionEnabled || $hasCollapsibleColumnsLayout || $isReordering,
                                        ])
                                    >
                                        <x-tables::reorder.handle
                                            :class="
                                                \Illuminate\Support\Arr::toCssClasses([
                                                    'absolute top-3 right-3 rtl:right-auto rtl:left-3',
                                                    'md:relative md:top-0 md:right-0 rtl:md:left-0' => ! $contentGrid,
                                                    'hidden' => ! $isReordering,
                                                ])
                                            "
                                        />

                                        @if ($isSelectionEnabled)
                                            <x-tables::checkbox
                                                x-model="selectedRecords"
                                                :value="$recordKey"
                                                :label="__('tables::table.fields.bulk_select_record.label', ['key' => $recordKey])"
                                                :class="
                                                    \Illuminate\Support\Arr::toCssClasses([
                                                        'filament-tables-record-checkbox absolute top-3 right-3 rtl:right-auto rtl:left-3',
                                                        'md:relative md:top-0 md:right-0 rtl:md:left-0' => ! $contentGrid,
                                                        'hidden' => $isReordering,
                                                    ])
                                                "
                                            />
                                        @endif

                                        @if ($hasCollapsibleColumnsLayout)
                                            <div
                                                @class([
                                                    'absolute right-1 rtl:left-1 rtl:right-auto',
                                                    'top-10' => $isSelectionEnabled,
                                                    'top-1' => ! $isSelectionEnabled,
                                                    'md:relative md:right-0 md:top-0 rtl:md:left-0' => ! $contentGrid,
                                                    'hidden' => $isReordering,
                                                ])
                                            >
                                                <x-tables::icon-button
                                                    icon="heroicon-s-chevron-down"
                                                    color="secondary"
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
                                                class="filament-tables-record-url-link block flex-1 py-3"
                                            >
                                                <x-tables::columns.layout
                                                    :components="$getColumnsLayout()"
                                                    :record="$record"
                                                    :record-key="$recordKey"
                                                    :row-loop="$loop"
                                                />
                                            </a>
                                        @elseif ($recordAction)
                                            @php
                                                if ($this->getCachedTableAction($recordAction)) {
                                                    $recordWireClickAction = "mountTableAction('{$recordAction}', '{$recordKey}')";
                                                } else {
                                                    $recordWireClickAction = "{$recordAction}('{$recordKey}')";
                                                }
                                            @endphp

                                            <button
                                                wire:click="{{ $recordWireClickAction }}"
                                                wire:target="{{ $recordWireClickAction }}"
                                                wire:loading.attr="disabled"
                                                wire:loading.class="cursor-wait opacity-70"
                                                type="button"
                                                class="filament-tables-record-action-button block flex-1 py-3"
                                            >
                                                <x-tables::columns.layout
                                                    :components="$getColumnsLayout()"
                                                    :record="$record"
                                                    :record-key="$recordKey"
                                                    :row-loop="$loop"
                                                />
                                            </button>
                                        @else
                                            <div class="flex-1 py-3">
                                                <x-tables::columns.layout
                                                    :components="$getColumnsLayout()"
                                                    :record="$record"
                                                    :record-key="$recordKey"
                                                    :row-loop="$loop"
                                                />
                                            </div>
                                        @endif

                                        @if (count($actions))
                                            <x-tables::actions
                                                :actions="$actions"
                                                :alignment="$actionsPosition === ActionsPosition::AfterContent ? 'left' : 'left md:right'"
                                                :record="$record"
                                                wrap="-md"
                                                :class="
                                                    \Illuminate\Support\Arr::toCssClasses([
                                                        'absolute bottom-1 right-1 rtl:right-auto rtl:left-1' => $actionsPosition === ActionsPosition::BottomCorner,
                                                        'md:relative md:bottom-0 md:right-0 rtl:md:left-0' => $actionsPosition === ActionsPosition::BottomCorner && (! $contentGrid),
                                                        'mb-3' => $actionsPosition === ActionsPosition::AfterContent,
                                                        'md:mb-0' => $actionsPosition === ActionsPosition::AfterContent && (! $contentGrid),
                                                        'hidden' => $isReordering,
                                                    ])
                                                "
                                            />
                                        @endif
                                    </div>

                                    @if ($hasCollapsibleColumnsLayout)
                                        <div
                                            x-show="! isCollapsed"
                                            x-collapse
                                            x-cloak
                                            @class([
                                                '-mx-2 pb-2',
                                                'md:pl-20 rtl:md:pl-0 rtl:md:pr-20' => (! $contentGrid) && $isSelectionEnabled,
                                                'md:pl-12 rtl:md:pl-0 rtl:md:pr-12' => (! $contentGrid) && (! $isSelectionEnabled),
                                                'hidden' => $isReordering,
                                            ])
                                        >
                                            {{ $collapsibleColumnsLayout->viewData(['recordKey' => $recordKey]) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </x-filament-support::grid>
                @endif

                @if (($content || $hasColumnsLayout) && $contentFooter)
                    {{ $contentFooter->with(['columns' => $columns, 'records' => $records]) }}
                @endif
            @elseif (($records !== null) && count($records))
                <x-tables::table>
                    <x-slot name="header">
                        @if ($isReordering)
                            <th></th>
                        @else
                            @if (count($actions) && $actionsPosition === ActionsPosition::BeforeCells)
                                @if ($actionsColumnLabel)
                                    <x-tables::header-cell>
                                        {{ $actionsColumnLabel }}
                                    </x-tables::header-cell>
                                @else
                                    <th class="w-5"></th>
                                @endif
                            @endif

                            @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
                                <x-tables::checkbox.cell>
                                    <x-tables::checkbox
                                        :label="__('tables::table.fields.bulk_select_page.label')"
                                        x-on:click="toggleSelectRecordsOnPage"
                                        x-bind:checked="
                                            let recordsOnPage = getRecordsOnPage()

                                            if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                                                $el.checked = true

                                                return 'checked'
                                            }

                                            $el.checked = false

                                            return null
                                        "
                                    />
                                </x-tables::checkbox.cell>
                            @endif

                            @if (count($actions) && $actionsPosition === ActionsPosition::BeforeColumns)
                                @if ($actionsColumnLabel)
                                    <x-tables::header-cell>
                                        {{ $actionsColumnLabel }}
                                    </x-tables::header-cell>
                                @else
                                    <th class="w-5"></th>
                                @endif
                            @endif
                        @endif

                        @foreach ($columns as $column)
                            <x-tables::header-cell
                                :extra-attributes="$column->getExtraHeaderAttributes()"
                                :is-sort-column="$getSortColumn() === $column->getName()"
                                :name="$column->getName()"
                                :alignment="$column->getAlignment()"
                                :sortable="$column->isSortable() && (! $isReordering)"
                                :sort-direction="$getSortDirection()"
                                class="filament-table-header-cell-{{ \Illuminate\Support\Str::of($column->getName())->camel()->kebab() }} {{ $getHiddenClasses($column) }}"
                            >
                                {{ $column->getLabel() }}
                            </x-tables::header-cell>
                        @endforeach

                        @if (! $isReordering)
                            @if (count($actions) && $actionsPosition === ActionsPosition::AfterColumns)
                                @if ($actionsColumnLabel)
                                    <x-tables::header-cell alignment="right">
                                        {{ $actionsColumnLabel }}
                                    </x-tables::header-cell>
                                @else
                                    <th class="w-5"></th>
                                @endif
                            @endif

                            @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
                                <x-tables::checkbox.cell>
                                    <x-tables::checkbox
                                        :label="__('tables::table.fields.bulk_select_page.label')"
                                        x-on:click="toggleSelectRecordsOnPage"
                                        x-bind:checked="
                                            let recordsOnPage = getRecordsOnPage()

                                            if (recordsOnPage.length && areRecordsSelected(recordsOnPage)) {
                                                $el.checked = true

                                                return 'checked'
                                            }

                                            $el.checked = false

                                            return null
                                        "
                                    />
                                </x-tables::checkbox.cell>
                            @endif

                            @if (count($actions) && $actionsPosition === ActionsPosition::AfterCells)
                                @if ($actionsColumnLabel)
                                    <x-tables::header-cell alignment="right">
                                        {{ $actionsColumnLabel }}
                                    </x-tables::header-cell>
                                @else
                                    <th class="w-5"></th>
                                @endif
                            @endif
                        @endif
                    </x-slot>

                    @if ($isColumnSearchVisible)
                        <x-tables::row>
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
                                <x-tables::cell
                                    class="filament-table-individual-search-cell-{{ \Illuminate\Support\Str::of($column->getName())->camel()->kebab() }} px-4 py-1"
                                >
                                    @if ($column->isIndividuallySearchable())
                                        <x-tables::search-input
                                            wire-model="tableColumnSearchQueries.{{ $column->getName() }}"
                                        />
                                    @endif
                                </x-tables::cell>
                            @endforeach

                            @if (! $isReordering)
                                @if (count($actions) && in_array($actionsPosition, [ActionsPosition::AfterColumns, ActionsPosition::AfterCells]))
                                    <td></td>
                                @endif

                                @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
                                    <td></td>
                                @endif
                            @endif
                        </x-tables::row>
                    @endif

                    @if (($records !== null) && count($records))
                        @foreach ($records as $record)
                            @php
                                $recordAction = $getRecordAction($record);
                                $recordKey = $getRecordKey($record);
                                $recordUrl = $getRecordUrl($record);
                            @endphp

                            <x-tables::row
                                :record-action="$recordAction"
                                :record-url="$recordUrl"
                                :wire:key="$this->id . '.table.records.' . $recordKey"
                                :wire:sortable.item="$isReordering ? $recordKey : null"
                                :wire:sortable.handle="$isReordering"
                                :striped="$isStriped"
                                x-bind:class="{
                                    'bg-gray-50 {{ config('tables.dark_mode') ? 'dark:bg-gray-500/10' : '' }}': isRecordSelected('{{ $recordKey }}'),
                                }"
                                :class="
                                    \Illuminate\Support\Arr::toCssClasses(array_merge(
                                        [
                                            'group cursor-move' => $isReordering,
                                        ],
                                        $getRecordClasses($record),
                                    ))
                                "
                            >
                                <x-tables::reorder.cell
                                    :class="
                                        \Illuminate\Support\Arr::toCssClasses([
                                            'hidden' => ! $isReordering,
                                        ])
                                    "
                                >
                                    @if ($isReordering)
                                        <x-tables::reorder.handle />
                                    @endif
                                </x-tables::reorder.cell>

                                @if (count($actions) && $actionsPosition === ActionsPosition::BeforeCells)
                                    <x-tables::actions.cell
                                        :class="
                                            \Illuminate\Support\Arr::toCssClasses([
                                                'hidden' => $isReordering,
                                            ])
                                        "
                                    >
                                        <x-tables::actions
                                            :actions="$actions"
                                            :record="$record"
                                        />
                                    </x-tables::actions.cell>
                                @endif

                                @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
                                    @if ($isRecordSelectable($record))
                                        <x-tables::checkbox.cell
                                            :class="
                                                \Illuminate\Support\Arr::toCssClasses([
                                                    'hidden' => $isReordering,
                                                ])
                                            "
                                        >
                                            <x-tables::checkbox
                                                x-model="selectedRecords"
                                                :value="$recordKey"
                                                :label="__('tables::table.fields.bulk_select_record.label', ['key' => $recordKey])"
                                                class="filament-tables-record-checkbox"
                                            />
                                        </x-tables::checkbox.cell>
                                    @else
                                        <x-tables::cell />
                                    @endif
                                @endif

                                @if (count($actions) && $actionsPosition === ActionsPosition::BeforeColumns)
                                    <x-tables::actions.cell
                                        :class="
                                            \Illuminate\Support\Arr::toCssClasses([
                                                'hidden' => $isReordering,
                                            ])
                                        "
                                    >
                                        <x-tables::actions
                                            :actions="$actions"
                                            :record="$record"
                                        />
                                    </x-tables::actions.cell>
                                @endif

                                @foreach ($columns as $column)
                                    @php
                                        $column->record($record);
                                        $column->rowLoop($loop->parent);
                                    @endphp

                                    <x-tables::cell
                                        wire:key="{{ $this->id }}.table.record.{{ $recordKey }}.column.{{ $column->getName() }}"
                                        wire:loading.remove.delay
                                        wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                        class="filament-table-cell-{{ \Illuminate\Support\Str::of($column->getName())->camel()->kebab() }} {{ $getHiddenClasses($column) }}"
                                        :attributes="\Filament\Support\prepare_inherited_attributes($column->getExtraCellAttributeBag())"
                                    >
                                        <x-tables::columns.column
                                            :column="$column"
                                            :record="$record"
                                            :record-action="$recordAction"
                                            :record-key="$recordKey"
                                            :record-url="$recordUrl"
                                            :is-click-disabled="$column->isClickDisabled() || $isReordering"
                                        />
                                    </x-tables::cell>
                                @endforeach

                                @if (count($actions) && $actionsPosition === ActionsPosition::AfterColumns)
                                    <x-tables::actions.cell
                                        :class="
                                            \Illuminate\Support\Arr::toCssClasses([
                                                'hidden' => $isReordering,
                                            ])
                                        "
                                    >
                                        <x-tables::actions
                                            :actions="$actions"
                                            :record="$record"
                                        />
                                    </x-tables::actions.cell>
                                @endif

                                @if ($isSelectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
                                    @if ($isRecordSelectable($record))
                                        <x-tables::checkbox.cell
                                            :class="
                                                \Illuminate\Support\Arr::toCssClasses([
                                                    'hidden' => $isReordering,
                                                ])
                                            "
                                        >
                                            <x-tables::checkbox
                                                x-model="selectedRecords"
                                                :value="$recordKey"
                                                :label="__('tables::table.fields.bulk_select_record.label', ['key' => $recordKey])"
                                                class="filament-tables-record-checkbox"
                                            />
                                        </x-tables::checkbox.cell>
                                    @else
                                        <x-tables::cell />
                                    @endif
                                @endif

                                @if (count($actions) && $actionsPosition === ActionsPosition::AfterCells)
                                    <x-tables::actions.cell
                                        :class="
                                            \Illuminate\Support\Arr::toCssClasses([
                                                'hidden' => $isReordering,
                                            ])
                                        "
                                    >
                                        <x-tables::actions
                                            :actions="$actions"
                                            :record="$record"
                                        />
                                    </x-tables::actions.cell>
                                @endif

                                <x-tables::loading-cell
                                    :colspan="$columnsCount"
                                    wire:loading.class.remove.delay="hidden"
                                    class="hidden"
                                    :wire:key="$this->id . '.table.records.' . $recordKey . '.loading-cell'"
                                    wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                />
                            </x-tables::row>
                        @endforeach

                        @if ($contentFooter)
                            <x-slot name="footer">
                                {{ $contentFooter->with(['columns' => $columns, 'records' => $records]) }}
                            </x-slot>
                        @endif
                    @endif
                </x-tables::table>
            @elseif ($records === null)
                <div
                    class="filament-tables-defer-loading-indicator flex items-center justify-center p-6"
                >
                    <div
                        @class([
                            'flex h-16 w-16 items-center justify-center rounded-full bg-primary-50 text-primary-500',
                            'dark:bg-gray-700' => config('tables.dark_mode'),
                        ])
                    >
                        <x-filament-support::loading-indicator
                            class="h-6 w-6"
                        />
                    </div>
                </div>
            @else
                @if ($emptyState = $getEmptyState())
                    {{ $emptyState }}
                @else
                    <div class="flex items-center justify-center p-4">
                        <x-tables::empty-state
                            :icon="$getEmptyStateIcon()"
                            :actions="$getEmptyStateActions()"
                            :column-searches="$hasColumnSearches()"
                        >
                            <x-slot name="heading">
                                {{ $getEmptyStateHeading() }}
                            </x-slot>

                            <x-slot name="description">
                                {{ $getEmptyStateDescription() }}
                            </x-slot>
                        </x-tables::empty-state>
                    </div>
                @endif
            @endif
        </div>

        @if ($records instanceof \Illuminate\Contracts\Pagination\Paginator &&
             ((! $records instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) || $records->total()))
            <div
                @class([
                    'filament-tables-pagination-container border-t p-2',
                    'dark:border-gray-700' => config('tables.dark_mode'),
                ])
            >
                <x-tables::pagination
                    :paginator="$records"
                    :records-per-page-select-options="$getRecordsPerPageSelectOptions()"
                />
            </div>
        @endif

        @if ($hasFiltersAfterContent)
            <div class="px-2 pb-2">
                <x-tables::hr />

                <div class="mt-2 p-4">
                    <x-tables::filters :form="$getFiltersForm()" />
                </div>
            </div>
        @endif
    </x-tables::container>

    <form wire:submit.prevent="callMountedTableAction">
        @php
            $action = $getMountedAction();
        @endphp

        <x-tables::modal
            :id="$this->id . '-table-action'"
            :wire:key="$action ? $this->id . '.table.actions.' . $action->getName() . '.modal' : null"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            :slide-over="$action?->isModalSlideOver()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            display-classes="block"
            x-init="livewire = $wire.__instance"
            x-on:modal-closed.stop="
                if ('mountedTableAction' in livewire?.serverMemo.data) {
                    livewire.set('mountedTableAction', null)
                }

                if ('mountedTableActionRecord' in livewire?.serverMemo.data) {
                    livewire.set('mountedTableActionRecord', null)
                }
            "
        >
            @if ($action)
                @if ($action->isModalCentered())
                    @if ($heading = $action->getModalHeading())
                        <x-slot name="heading">
                            {{ $heading }}
                        </x-slot>
                    @endif

                    @if ($subheading = $action->getModalSubheading())
                        <x-slot name="subheading">
                            {{ $subheading }}
                        </x-slot>
                    @endif
                @else
                    <x-slot name="header">
                        @if ($heading = $action->getModalHeading())
                            <x-tables::modal.heading>
                                {{ $heading }}
                            </x-tables::modal.heading>
                        @endif

                        @if ($subheading = $action->getModalSubheading())
                            <x-tables::modal.subheading>
                                {{ $subheading }}
                            </x-tables::modal.subheading>
                        @endif
                    </x-slot>
                @endif

                {{ $action->getModalContent() }}

                @if ($action->hasFormSchema())
                    {{ $getMountedActionForm() }}
                @endif

                {{ $action->getModalFooter() }}

                @if (count($action->getModalActions()))
                    <x-slot name="footer">
                        <x-tables::modal.actions
                            :full-width="$action->isModalCentered()"
                        >
                            @foreach ($action->getModalActions() as $modalAction)
                                {{ $modalAction }}
                            @endforeach
                        </x-tables::modal.actions>
                    </x-slot>
                @endif
            @endif
        </x-tables::modal>
    </form>

    <form wire:submit.prevent="callMountedTableBulkAction">
        @php
            $action = $getMountedBulkAction();
        @endphp

        <x-tables::modal
            :id="$this->id . '-table-bulk-action'"
            :wire:key="$action ? $this->id . '.table.bulk-actions.' . $action->getName() . '.modal' : null"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            :slide-over="$action?->isModalSlideOver()"
            :close-by-clicking-away="$action?->isModalClosedByClickingAway()"
            display-classes="block"
            x-init="livewire = $wire.__instance"
            x-on:modal-closed.stop="if ('mountedTableBulkAction' in livewire?.serverMemo.data) livewire.set('mountedTableBulkAction', null)"
        >
            @if ($action)
                @if ($action->isModalCentered())
                    @if ($heading = $action->getModalHeading())
                        <x-slot name="heading">
                            {{ $heading }}
                        </x-slot>
                    @endif

                    @if ($subheading = $action->getModalSubheading())
                        <x-slot name="subheading">
                            {{ $subheading }}
                        </x-slot>
                    @endif
                @else
                    <x-slot name="header">
                        @if ($heading = $action->getModalHeading())
                            <x-tables::modal.heading>
                                {{ $heading }}
                            </x-tables::modal.heading>
                        @endif

                        @if ($subheading = $action->getModalSubheading())
                            <x-tables::modal.subheading>
                                {{ $subheading }}
                            </x-tables::modal.subheading>
                        @endif
                    </x-slot>
                @endif

                {{ $action->getModalContent() }}

                @if ($action->hasFormSchema())
                    {{ $getMountedBulkActionForm() }}
                @endif

                {{ $action->getModalFooter() }}

                @if (count($action->getModalActions()))
                    <x-slot name="footer">
                        <x-tables::modal.actions
                            :full-width="$action->isModalCentered()"
                        >
                            @foreach ($action->getModalActions() as $modalAction)
                                {{ $modalAction }}
                            @endforeach
                        </x-tables::modal.actions>
                    </x-slot>
                @endif
            @endif
        </x-tables::modal>
    </form>

    @if (! $this instanceof \Filament\Tables\Contracts\RendersFormComponentActionModal)
        {{ $this->modal }}
    @endif
</div>
