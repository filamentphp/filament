@php
    use Filament\Tables\Actions\Position as ActionsPosition;
    use Filament\Tables\Filters\Layout as FiltersLayout;

    $actions = $getActions();
    $actionsAlignment = $getActionsAlignment();
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
    $isStriped = $isStriped();
    $hasFilters = $isFilterable();
    $hasFiltersDropdown = $hasFilters && ($getFiltersLayout() === FiltersLayout::Dropdown);
    $hasFiltersAboveContent = $hasFilters && ($getFiltersLayout() === FiltersLayout::AboveContent);
    $hasFiltersAfterContent = $hasFilters && ($getFiltersLayout() === FiltersLayout::BelowContent);
    $isColumnToggleFormVisible = $hasToggleableColumns();
    $records = $getRecords();

    $columnsCount = count($columns);
    if (count($actions) && (! $isReordering)) $columnsCount++;
    if ($isSelectionEnabled || $isReordering) $columnsCount++;

    $getHiddenClasses = function (\Filament\Tables\Columns\Column $column): ?string {
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

            for (checkbox of $el.getElementsByClassName('filament-tables-record-checkbox')) {
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

            this.selectedRecords = await $wire.getAllTableRecordKeys()

            this.isLoading = false
        },

        deselectAllRecords: function () {
            this.selectedRecords = []
        },

        isRecordSelected: function (key) {
            return this.selectedRecords.includes(key)
        },

        areRecordsSelected: function (keys) {
            return keys.every(key => this.isRecordSelected(key))
        },

    }"
    class="filament-tables-component"
>
    <x-filament-tables::container>
        <div
            class="filament-tables-header-container"
            x-show="hasHeader = (@js($renderHeader = ($header || $heading || ($headerActions && (! $isReordering)) || $isReorderable || $isGlobalSearchVisible || $hasFilters || $isColumnToggleFormVisible)) || selectedRecords.length)"
            {!! ! $renderHeader ? 'x-cloak' : null !!}
        >
            @if ($header)
                {{ $header }}
            @elseif ($heading || $headerActions)
                <div @class([
                    'px-2 pt-2',
                    'hidden' => ! $heading && $isReordering,
                ])>
                    <x-filament-tables::header :actions="$isReordering ? [] : $headerActions" class="mb-2">
                        <x-slot name="heading">
                            {{ $heading }}
                        </x-slot>

                        <x-slot name="description">
                            {{ $description }}
                        </x-slot>
                    </x-filament-tables::header>

                    <x-filament::hr :x-show="\Illuminate\Support\Js::from($isReorderable || $isGlobalSearchVisible || $hasFilters || $isColumnToggleFormVisible) . ' || selectedRecords.length'" />
                </div>
            @endif

            @if ($hasFiltersAboveContent)
                <div class="px-2 pt-2">
                    <div class="p-4 mb-2">
                        <x-filament-tables::filters :form="$getFiltersForm()" />
                    </div>

                    <x-filament::hr :x-show="\Illuminate\Support\Js::from($isReorderable || $isGlobalSearchVisible || $isColumnToggleFormVisible) . ' || selectedRecords.length'" />
                </div>
            @endif

            <div
                x-show="@js($shouldRenderHeaderDiv = ($isReorderable || $isGlobalSearchVisible || $hasFiltersDropdown || $isColumnToggleFormVisible)) || selectedRecords.length"
                {!! ! $shouldRenderHeaderDiv ? 'x-cloak' : null !!}
                class="flex items-center justify-between p-2 h-14"
                x-bind:class="{
                    'gap-2': @js($isReorderable) || selectedRecords.length,
                }"
            >
                <div class="flex items-center gap-2">
                    @if ($isReorderable)
                        <x-filament-tables::reorder.trigger
                            :enabled="$isReordering"
                        />
                    @endif

                    @if (! $isReordering)
                        <x-filament-tables::bulk-actions
                            x-show="selectedRecords.length"
                            :actions="$getBulkActions()"
                        />
                    @endif
                </div>

                @if ($isGlobalSearchVisible || $hasFiltersDropdown || $isColumnToggleFormVisible)
                    <div class="flex items-center justify-end w-full gap-2 md:max-w-md">
                        @if ($isGlobalSearchVisible)
                            <div class="flex items-center justify-end flex-1">
                                <x-filament-tables::search-input />
                            </div>
                        @endif

                        @if ($hasFiltersDropdown)
                            <x-filament-tables::filters.dropdown
                                :form="$getFiltersForm()"
                                :width="$getFiltersFormWidth()"
                                :indicators-count="count(\Illuminate\Support\Arr::flatten($filterIndicators))"
                                class="shrink-0"
                            />
                        @endif

                        @if ($isColumnToggleFormVisible)
                            <x-filament-tables::toggleable
                                :form="$getColumnToggleForm()"
                                :width="$getColumnToggleFormWidth()"
                                class="shrink-0"
                            />
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @if ($isReordering)
            <x-filament-tables::reorder.indicator
                :colspan="$columnsCount"
                class="border-t dark:border-gray-700"
            />
        @elseif ($isSelectionEnabled)
            <x-filament-tables::selection-indicator
                :all-records-count="$getAllRecordsCount()"
                :colspan="$columnsCount"
                x-show="selectedRecords.length"
                class="border-t dark:border-gray-700"
            >
                <x-slot name="selectedRecordsCount">
                    <span x-text="selectedRecords.length"></span>
                </x-slot>
            </x-filament-tables::selection-indicator>
        @endif

        <x-filament-tables::filters.indicators
            :indicators="$filterIndicators"
            class="border-t dark:border-gray-700"
        />

        <div
            @if ($pollingInterval = $getPollingInterval())
                wire:poll.{{ $pollingInterval }}
            @endif
            @class([
                'filament-tables-table-container overflow-x-auto relative dark:border-gray-700',
                'overflow-x-auto' => $content || $hasColumnsLayout,
                'rounded-t-xl' => ! $renderHeader,
                'border-t' => $renderHeader,
            ])
            x-bind:class="{
                'rounded-t-xl': ! hasHeader,
                'border-t': hasHeader,
            }"
        >
            @if ($content || $hasColumnsLayout)
                @if (count($records))
                    @if (($content || $hasColumnsLayout) && (! $isReordering))
                        <div class="bg-gray-500/5 flex items-center gap-4 px-4 border-b dark:border-gray-700">
                            @if ($isSelectionEnabled)
                                <x-filament-tables::checkbox
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
                                    @class(['hidden' => $isReordering])
                                />
                            @endif

                            @php
                                $sortableColumns = array_filter(
                                    $columns,
                                    fn (\Filament\Tables\Columns\Column $column): bool => $column->isSortable(),
                                );
                            @endphp

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
                                            {{ __('filament-tables::table.sorting.fields.column.label') }}
                                        </span>

                                        <select
                                            x-model="column"
                                            style="background-position: right 0.2rem center"
                                            class="text-xs pl-2 pr-6 py-1 font-medium border-0 bg-gray-500/5 rounded-lg border-gray-300 sm:text-sm focus:ring-0 focus:border-primary-500 focus:ring-primary-500 dark:text-white dark:bg-gray-700 dark:border-gray-600 dark:focus:border-primary-500"
                                        >
                                            <option value="">-</option>
                                            @foreach ($sortableColumns as $column)
                                                <option value="{{ $column->getName() }}">{{ $column->getLabel() }}</option>
                                            @endforeach
                                        </select>
                                    </label>

                                    <label>
                                        <span class="sr-only">
                                            {{ __('filament-tables::table.sorting.fields.direction.label') }}
                                        </span>

                                        <select
                                            x-show="column"
                                            x-model="direction"
                                            style="background-position: right 0.2rem center"
                                            class="text-xs pl-2 pr-6 py-1 font-medium border-0 bg-gray-500/5 rounded-lg border-gray-300 sm:text-sm focus:ring-0 focus:border-primary-500 focus:ring-primary-500 dark:text-white dark:bg-gray-700 dark:border-gray-600 dark:focus:border-primary-500"
                                        >
                                            <option value="asc">{{ __('filament-tables::table.sorting.fields.direction.options.asc') }}</option>
                                            <option value="desc">{{ __('filament-tables::table.sorting.fields.direction.options.desc') }}</option>
                                        </select>
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
                                'dark:divide-gray-700',
                                'divide-y' => ! $contentGrid,
                                'p-2 gap-2' => $contentGrid,
                            ])
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
                                        x-data="{ isCollapsed: true }"
                                        x-init="$dispatch('collapsible-table-row-initialized')"
                                        x-on:expand-all-table-rows.window="isCollapsed = false"
                                        x-on:collapse-all-table-rows.window="isCollapsed = true"
                                    @endif
                                    wire:key="{{ $this->id }}.table.records.{{ $recordKey }}"
                                    @if ($isReordering)
                                        x-sortable-item="{{ $recordKey }}"
                                        x-sortable-handle
                                    @endif
                                >
                                    <div
                                        x-bind:class="{
                                            'bg-gray-50 dark:bg-gray-500/10': isRecordSelected('{{ $recordKey }}'),
                                        }"
                                        @class(array_merge(
                                            [
                                                'h-full relative px-4 transition',
                                                'hover:bg-gray-50 dark:hover:bg-gray-500/10' => $recordUrl || $recordAction,
                                                'dark:border-gray-600' => ! $contentGrid,
                                                'group' => $isReordering,
                                                'rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 dark:bg-gray-700/40' => $contentGrid,
                                            ],
                                            $getRecordClasses($record),
                                        ))
                                    >
                                        <div @class([
                                            'items-center gap-4 md:flex md:mr-0 rtl:md:ml-0' => (! $contentGrid),
                                            'mr-6 rtl:mr-0 rtl:ml-6' => $isSelectionEnabled || $hasCollapsibleColumnsLayout || $isReordering,
                                        ])>
                                            <x-filament-tables::reorder.handle @class([
                                                'absolute top-3 right-3 rtl:right-auto rtl:left-3',
                                                'md:relative md:top-0 md:right-0 rtl:md:left-0' => ! $contentGrid,
                                                'hidden' => ! $isReordering,
                                            ]) />

                                            @if ($isSelectionEnabled)
                                                <x-filament-tables::checkbox
                                                    x-model="selectedRecords"
                                                    :value="$recordKey"
                                                    @class([
                                                        'filament-tables-record-checkbox absolute top-3 right-3 rtl:right-auto rtl:left-3',
                                                        'md:relative md:top-0 md:right-0 rtl:md:left-0' => ! $contentGrid,
                                                        'hidden' => $isReordering,
                                                    ])
                                                />
                                            @endif

                                            @if ($hasCollapsibleColumnsLayout)
                                                <div @class([
                                                    'absolute right-1 rtl:right-auto rtl:left-1',
                                                    'top-10' => $isSelectionEnabled,
                                                    'top-1' => ! $isSelectionEnabled,
                                                    'md:relative md:top-0 md:right-0 rtl:md:left-0' => ! $contentGrid,
                                                    'hidden' => $isReordering,
                                                ])>
                                                    <x-filament::icon-button
                                                        icon="heroicon-m-chevron-down"
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
                                                    class="flex-1 block py-3"
                                                >
                                                    <x-filament-tables::columns.layout
                                                        :components="$getColumnsLayout()"
                                                        :record="$record"
                                                        :record-key="$recordKey"
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
                                                    wire:loading.class="opacity-70 cursor-wait"
                                                    type="button"
                                                    class="flex-1 block py-3"
                                                >
                                                    <x-filament-tables::columns.layout
                                                        :components="$getColumnsLayout()"
                                                        :record="$record"
                                                        :record-key="$recordKey"
                                                    />
                                                </button>
                                            @else
                                                <div class="flex-1 py-3">
                                                    <x-filament-tables::columns.layout
                                                        :components="$getColumnsLayout()"
                                                        :record="$record"
                                                        :record-key="$recordKey"
                                                    />
                                                </div>
                                            @endif

                                            @if (count($actions))
                                                <x-filament-tables::actions
                                                    :actions="$actions"
                                                    :alignment="$actionsPosition === ActionsPosition::AfterContent ? 'left' : 'left md:right'"
                                                    :record="$record"
                                                    wrap="-md"
                                                    @class([
                                                        'absolute bottom-1 right-1 rtl:right-auto rtl:left-1' => $actionsPosition === ActionsPosition::BottomCorner,
                                                        'md:relative md:bottom-0 md:right-0 rtl:md:left-0' => $actionsPosition === ActionsPosition::BottomCorner && (! $contentGrid),
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
                                                    'pb-2 -mx-2',
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
                        </x-filament::grid>
                    @endif

                    @if (($content || $hasColumnsLayout) && $contentFooter)
                        {{ $contentFooter->with(['columns' => $columns, 'records' => $records]) }}
                    @endif
                @else
                    @if ($emptyState = $getEmptyState())
                        {{ $emptyState }}
                    @else
                        <div class="flex items-center justify-center p-4">
                            <x-filament-tables::empty-state :icon="$getEmptyStateIcon()" :actions="$getEmptyStateActions()">
                                <x-slot name="heading">
                                    {{ $getEmptyStateHeading() }}
                                </x-slot>

                                <x-slot name="description">
                                    {{ $getEmptyStateDescription() }}
                                </x-slot>
                            </x-filament-tables::empty-state>
                        </div>
                    @endif
                @endif
            @else
                <x-filament-tables::table>
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

                            @if ($isSelectionEnabled)
                                <x-filament-tables::checkbox.cell>
                                    <x-filament-tables::checkbox
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
                                </x-filament-tables::checkbox.cell>
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

                        @foreach ($columns as $column)
                            <x-filament-tables::header-cell
                                :extra-attributes="$column->getExtraHeaderAttributes()"
                                :actively-sorted="$getSortColumn() === $column->getName()"
                                :name="$column->getName()"
                                :alignment="$column->getAlignment()"
                                :sortable="$column->isSortable() && (! $isReordering)"
                                :sort-direction="$getSortDirection()"
                                :class="$getHiddenClasses($column)"
                            >
                                {{ $column->getLabel() }}
                            </x-filament-tables::header-cell>
                        @endforeach

                        @if (count($actions) && (! $isReordering) && $actionsPosition === ActionsPosition::AfterCells)
                            @if ($actionsColumnLabel)
                                <x-filament-tables::header-cell alignment="right">
                                    {{ $actionsColumnLabel }}
                                </x-filament-tables::header-cell>
                            @else
                                <th class="w-5"></th>
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

                                @if ($isSelectionEnabled)
                                    <td></td>
                                @endif
                            @endif

                            @foreach ($columns as $column)
                                <x-filament-tables::cell class="px-4 py-1">
                                    @if ($column->isIndividuallySearchable())
                                        <x-filament-tables::search-input wire-model="tableColumnSearches.{{ $column->getName() }}" />
                                    @endif
                                </x-filament-tables::cell>
                            @endforeach

                            @if (count($actions) && (! $isReordering) && $actionsPosition === ActionsPosition::AfterCells)
                                <td></td>
                            @endif
                        </x-filament-tables::row>
                    @endif

                    @if (count($records))
                        @foreach ($records as $record)
                            @php
                                $recordAction = $getRecordAction($record);
                                $recordKey = $getRecordKey($record);
                                $recordUrl = $getRecordUrl($record);
                            @endphp

                            <x-filament-tables::row
                                :record-action="$recordAction"
                                :record-url="$recordUrl"
                                :wire:key="$this->id . '.table.records.' . $recordKey"
                                :x-sortable-item="$isReordering ? $recordKey : null"
                                :x-sortable-handle="$isReordering"
                                :striped="$isStriped"
                                x-bind:class="{
                                    'bg-gray-50 dark:bg-gray-500/10': isRecordSelected('{{ $recordKey }}'),
                                }"
                                @class(array_merge(
                                    [
                                        'group cursor-move' => $isReordering,
                                    ],
                                    $getRecordClasses($record),
                                ))
                            >
                                <x-filament-tables::reorder.cell @class([
                                    'hidden' => ! $isReordering,
                                ])>
                                    @if ($isReordering)
                                        <x-filament-tables::reorder.handle />
                                    @endif
                                </x-filament-tables::reorder.cell>

                                @if (count($actions) && $actionsPosition === ActionsPosition::BeforeCells)
                                    <x-filament-tables::actions.cell @class([
                                        'hidden' => $isReordering,
                                    ])>
                                        <x-filament-tables::actions
                                            :actions="$actions"
                                            :alignment="$actionsAlignment ?? 'left'"
                                            :record="$record"
                                        />
                                    </x-filament-tables::actions.cell>
                                @endif

                                @if ($isSelectionEnabled)
                                    <x-filament-tables::checkbox.cell @class([
                                        'hidden' => $isReordering,
                                    ])>
                                        <x-filament-tables::checkbox
                                            x-model="selectedRecords"
                                            :value="$recordKey"
                                            class="filament-tables-record-checkbox"
                                        />
                                    </x-filament-tables::checkbox.cell>
                                @endif

                                @if (count($actions) && $actionsPosition === ActionsPosition::BeforeColumns)
                                    <x-filament-tables::actions.cell @class([
                                        'hidden' => $isReordering,
                                    ])>
                                        <x-filament-tables::actions
                                            :actions="$actions"
                                            :alignment="$actionsAlignment ?? 'left'"
                                            :record="$record"
                                        />
                                    </x-filament-tables::actions.cell>
                                @endif

                                @foreach ($columns as $column)
                                    @php
                                        $column->record($record);
                                    @endphp

                                    <x-filament-tables::cell
                                        :class="$getHiddenClasses($column)"
                                        wire:loading.remove.delay
                                        wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
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

                                @if (count($actions) && $actionsPosition === ActionsPosition::AfterCells)
                                    <x-filament-tables::actions.cell @class([
                                        'hidden' => $isReordering,
                                    ])>
                                        <x-filament-tables::actions
                                            :actions="$actions"
                                            :alignment="$actionsAlignment ?? 'right'"
                                            :record="$record"
                                        />
                                    </x-filament-tables::actions.cell>
                                @endif

                                <x-filament-tables::loading-cell
                                    :colspan="$columnsCount"
                                    wire:loading.class.remove.delay="hidden"
                                    class="hidden"
                                    :wire:key="$this->id . '.table.records.' . $recordKey . '.loading-cell'"
                                    wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
                                />
                            </x-filament-tables::row>
                        @endforeach

                        @if ($contentFooter)
                            <x-slot name="footer">
                                {{ $contentFooter->with(['columns' => $columns, 'records' => $records]) }}
                            </x-slot>
                        @endif
                    @else
                        @if ($emptyState = $getEmptyState())
                            {{ $emptyState }}
                        @else
                            <tr>
                                <td colspan="{{ $columnsCount }}">
                                    <div class="flex items-center justify-center p-4 w-full">
                                        <x-filament-tables::empty-state :icon="$getEmptyStateIcon()" :actions="$getEmptyStateActions()">
                                            <x-slot name="heading">
                                                {{ $getEmptyStateHeading() }}
                                            </x-slot>

                                            <x-slot name="description">
                                                {{ $getEmptyStateDescription() }}
                                            </x-slot>
                                        </x-filament-tables::empty-state>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endif
                </x-filament-tables::table>
            @endif
        </div>

        @if (
            $records instanceof \Illuminate\Contracts\Pagination\Paginator &&
            ((! $records instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) || $records->total())
        )
            <div class="filament-tables-pagination-container p-2 border-t dark:border-gray-700">
                <x-filament-tables::pagination
                    :paginator="$records"
                    :page-options="$getPaginationPageOptions()"
                />
            </div>
        @endif

        @if ($hasFiltersAfterContent)
            <div class="px-2 pb-2">
                <x-filament::hr />

                <div class="p-4 mt-2">
                    <x-filament-tables::filters :form="$getFiltersForm()" />
                </div>
            </div>
        @endif
    </x-filament-tables::container>

    <form wire:submit.prevent="callMountedTableAction">
        @php
            $action = $getMountedAction();
        @endphp

        <x-filament::modal
            :id="$this->id . '-table-action'"
            :wire:key="$action ? $this->id . '.table.actions.' . $action->getName() . '.modal' : null"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            :slide-over="$action?->isModalSlideOver()"
            display-classes="block"
        >
            @if ($action)
                @if ($action->isModalCentered())
                    <x-slot name="heading">
                        {{ $action->getModalHeading() }}
                    </x-slot>

                    @if ($subheading = $action->getModalSubheading())
                        <x-slot name="subheading">
                            {{ $subheading }}
                        </x-slot>
                    @endif
                @else
                    <x-slot name="header">
                        <x-filament::modal.heading>
                            {{ $action->getModalHeading() }}
                        </x-filament::modal.heading>

                        @if ($subheading = $action->getModalSubheading())
                            <x-filament::modal.subheading>
                                {{ $subheading }}
                            </x-filament::modal.subheading>
                        @endif
                    </x-slot>
                @endif

                {{ $action->getModalContent() }}

                @if ($this->mountedTableActionHasForm())
                    {{ $getMountedActionForm() }}
                @endif

                @if (count($action->getModalActions()))
                    <x-slot name="footer">
                        <x-filament::modal.actions :full-width="$action->isModalCentered()">
                            @foreach ($action->getModalActions() as $modalAction)
                                {{ $modalAction }}
                            @endforeach
                        </x-filament::modal.actions>
                    </x-slot>
                @endif
            @endif
        </x-filament::modal>
    </form>

    <form wire:submit.prevent="callMountedTableBulkAction">
        @php
            $action = $getMountedBulkAction();
        @endphp

        <x-filament::modal
            :id="$this->id . '-table-bulk-action'"
            :wire:key="$action ? $this->id . '.table.bulk-actions.' . $action->getName() . '.modal' : null"
            :visible="filled($action)"
            :width="$action?->getModalWidth()"
            :slide-over="$action?->isModalSlideOver()"
            display-classes="block"
        >
            @if ($action)
                @if ($action->isModalCentered())
                    <x-slot name="heading">
                        {{ $action->getModalHeading() }}
                    </x-slot>

                    @if ($subheading = $action->getModalSubheading())
                        <x-slot name="subheading">
                            {{ $subheading }}
                        </x-slot>
                    @endif
                @else
                    <x-slot name="header">
                        <x-filament::modal.heading>
                            {{ $action->getModalHeading() }}
                        </x-filament::modal.heading>

                        @if ($subheading = $action->getModalSubheading())
                            <x-filament::modal.subheading>
                                {{ $subheading }}
                            </x-filament::modal.subheading>
                        @endif
                    </x-slot>
                @endif

                {{ $action->getModalContent() }}

                @if ($this->mountedTableBulkActionHasForm())
                    {{ $getMountedBulkActionForm() }}
                @endif

                @if (count($action->getModalActions()))
                    <x-slot name="footer">
                        <x-filament::modal.actions :full-width="$action->isModalCentered()">
                            @foreach ($action->getModalActions() as $modalAction)
                                {{ $modalAction }}
                            @endforeach
                        </x-filament::modal.actions>
                    </x-slot>
                @endif
            @endif
        </x-filament::modal>
    </form>

    @if (! $this instanceof \Filament\Tables\Contracts\RendersFormComponentActionModal)
        {{ $this->formsModal }}
    @endif
</div>
