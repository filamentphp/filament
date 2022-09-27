@php
    use Filament\Tables\Actions\Position as ActionsPosition;
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
    $isStriped = $isStriped();
    $hasFilters = $isFilterable();
    $hasFiltersPopover = $hasFilters && ($getFiltersLayout() === FiltersLayout::Popover);
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
    <x-tables::container>
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
                    <x-tables::header :actions="$isReordering ? [] : $headerActions" class="mb-2">
                        <x-slot name="heading">
                            {{ $heading }}
                        </x-slot>

                        <x-slot name="description">
                            {{ $description }}
                        </x-slot>
                    </x-tables::header>

                    <x-tables::hr :x-show="\Illuminate\Support\Js::from($isReorderable || $isGlobalSearchVisible || $hasFilters || $isColumnToggleFormVisible) . ' || selectedRecords.length'" />
                </div>
            @endif

            @if ($hasFiltersAboveContent)
                <div class="px-2 pt-2">
                    <div class="p-4 mb-2">
                        <x-tables::filters :form="$getFiltersForm()" />
                    </div>

                    <x-tables::hr :x-show="\Illuminate\Support\Js::from($isReorderable || $isGlobalSearchVisible || $isColumnToggleFormVisible) . ' || selectedRecords.length'" />
                </div>
            @endif

            <div
                x-show="@js($shouldRenderHeaderDiv = ($isReorderable || $isGlobalSearchVisible || $hasFiltersPopover || $isColumnToggleFormVisible)) || selectedRecords.length"
                {!! ! $shouldRenderHeaderDiv ? 'x-cloak' : null !!}
                class="flex items-center justify-between p-2 h-14"
                x-bind:class="{
                    'gap-2': @js($isReorderable) || selectedRecords.length,
                }"
            >
                <div class="flex items-center gap-2">
                    @if ($isReorderable)
                        <x-tables::reorder.trigger
                            :enabled="$isReordering"
                        />
                    @endif

                    @if (! $isReordering)
                        <x-tables::bulk-actions
                            x-show="selectedRecords.length"
                            :actions="$getBulkActions()"
                        />
                    @endif
                </div>

                @if ($isGlobalSearchVisible || $hasFiltersPopover || $isColumnToggleFormVisible)
                    <div class="flex items-center justify-end w-full gap-2 md:max-w-md">
                        @if ($isGlobalSearchVisible)
                            <div class="flex items-center justify-end flex-1">
                                <x-tables::search-input />
                            </div>
                        @endif

                        @if ($hasFiltersPopover)
                            <x-tables::filters.popover
                                :form="$getFiltersForm()"
                                :width="$getFiltersFormWidth()"
                                :indicators-count="count(\Illuminate\Support\Arr::flatten($filterIndicators))"
                                class="shrink-0"
                            />
                        @endif

                        @if ($isColumnToggleFormVisible)
                            <x-tables::toggleable
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
            <x-tables::reorder.indicator
                :colspan="$columnsCount"
                :class="\Illuminate\Support\Arr::toCssClasses([
                    'border-t',
                    'dark:border-gray-700' => config('tables.dark_mode'),
                ])"
            />
        @elseif ($isSelectionEnabled)
            <x-tables::selection-indicator
                :all-records-count="$getAllRecordsCount()"
                :colspan="$columnsCount"
                x-show="selectedRecords.length"
                :class="\Illuminate\Support\Arr::toCssClasses([
                    'border-t',
                    'dark:border-gray-700' => config('tables.dark_mode'),
                ])"
            >
                <x-slot name="selectedRecordsCount">
                    <span x-text="selectedRecords.length"></span>
                </x-slot>
            </x-tables::selection-indicator>
        @endif

        <x-tables::filters.indicators
            :indicators="$filterIndicators"
            :class="\Illuminate\Support\Arr::toCssClasses([
                'border-t',
                'dark:border-gray-700' => config('tables.dark_mode'),
            ])"
        />

        <div
            @if ($pollingInterval = $getPollingInterval())
                wire:poll.{{ $pollingInterval }}
            @endif
            @class([
                'filament-tables-table-container overflow-x-auto relative',
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
            @if (count($records))
                @if (($content || $hasColumnsLayout) && (! $isReordering))
                    <div @class([
                        'bg-gray-500/5 flex items-center gap-4 px-4 border-b',
                        'dark:border-gray-700' => config('tables.dark_mode'),
                    ])>
                        @if ($isSelectionEnabled)
                            <x-tables::checkbox
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
                                :class="\Illuminate\Support\Arr::toCssClasses([
                                    'hidden' => $isReordering,
                                ])"
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
                                        {{ __('tables::table.sorting.fields.column.label') }}
                                    </span>

                                    <select
                                        x-model="column"
                                        style="background-position: right 0.2rem center"
                                        class="text-xs pl-2 pr-6 py-1 font-medium border-0 bg-gray-500/5 rounded-lg focus:ring-0 sm:text-sm"
                                    >
                                        <option value="">-</option>
                                        @foreach ($sortableColumns as $column)
                                            <option value="{{ $column->getName() }}">{{ $column->getLabel() }}</option>
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
                                        style="background-position: right 0.2rem center"
                                        class="text-xs pl-2 pr-6 py-1 font-medium border-0 bg-gray-500/5 rounded-lg focus:ring-0 sm:text-sm"
                                    >
                                        <option value="asc">{{ __('tables::table.sorting.fields.direction.options.asc') }}</option>
                                        <option value="desc">{{ __('tables::table.sorting.fields.direction.options.desc') }}</option>
                                    </select>
                                </label>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($content)
                    {{ $content->with(['records' => $records]) }}
                @elseif ($hasColumnsLayout)
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
                        :class="\Illuminate\Support\Arr::toCssClasses([
                            'divide-y' => ! $contentGrid,
                            'p-2 gap-2' => $contentGrid,
                            'dark:divide-gray-700' => config('tables.dark_mode'),
                        ])"
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
                                @endif
                                wire:key="{{ $this->id }}.table.records.{{ $recordKey }}"
                                @if ($isReordering)
                                    wire:sortable.item="{{ $recordKey }}"
                                    wire:sortable.handle
                                @endif
                            >
                                <div
                                    x-bind:class="{
                                        'bg-gray-50 {{ config('tables.dark_mode') ? 'dark:bg-gray-500/10' : '' }}': isRecordSelected('{{ $recordKey }}'),
                                    }"
                                    @class(array_merge(
                                        [
                                            'h-full relative px-4 transition',
                                            'hover:bg-gray-50' => $recordUrl || $recordAction,
                                            'dark:hover:bg-gray-500/10' => ($recordUrl || $recordAction) && config('tables.dark_mode'),
                                            'dark:border-gray-600 dark:bg-gray-700' => (! $contentGrid) && config('tables.dark_mode'),
                                            'group' => $isReordering,
                                            'rounded-xl shadow-sm border border-gray-200' => $contentGrid,
                                            'dark:border-gray-700' => $contentGrid && config('tables.dark_mode'),
                                        ],
                                        $getRecordClasses($record),
                                    ))
                                >
                                    <div @class([
                                        'items-center gap-4 md:flex md:mr-0 rtl:md:ml-0' => (! $contentGrid),
                                        'mr-6 rtl:mr-0 rtl:ml-6' => $isSelectionEnabled || $hasCollapsibleColumnsLayout || $isReordering,
                                    ])>
                                        <x-tables::reorder.handle :class="\Illuminate\Support\Arr::toCssClasses([
                                            'absolute top-3 right-3 rtl:right-auto rtl:left-3',
                                            'md:relative md:top-0 md:right-0 rtl:md:left-0' => ! $contentGrid,
                                            'hidden' => ! $isReordering,
                                        ])" />

                                        @if ($isSelectionEnabled)
                                            <x-tables::checkbox
                                                x-model="selectedRecords"
                                                :value="$recordKey"
                                                :class="\Illuminate\Support\Arr::toCssClasses([
                                                    'filament-tables-record-checkbox absolute top-3 right-3 rtl:right-auto rtl:left-3',
                                                    'md:relative md:top-0 md:right-0 rtl:md:left-0' => ! $contentGrid,
                                                    'hidden' => $isReordering,
                                                ])"
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
                                                class="flex-1 block py-3"
                                            >
                                                <x-tables::columns.layout
                                                    :components="$getColumnsLayout()"
                                                    :record="$record"
                                                    :record-key="$recordKey"
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
                                                wire:loading.class="opacity-70 cursor-wait"
                                                type="button"
                                                class="flex-1 block py-3"
                                            >
                                                <x-tables::columns.layout
                                                    :components="$getColumnsLayout()"
                                                    :record="$record"
                                                    :record-key="$recordKey"
                                                />
                                            </button>
                                        @else
                                            <div class="flex-1 py-3">
                                                <x-tables::columns.layout
                                                    :components="$getColumnsLayout()"
                                                    :record="$record"
                                                    :record-key="$recordKey"
                                                />
                                            </div>
                                        @endif

                                        @if (count($actions))
                                            <x-tables::actions
                                                :actions="$actions"
                                                :alignment="$actionsPosition === ActionsPosition::AfterContent ? 'left' : 'left md:right'"
                                                :record="$record"
                                                wrap="-md"
                                                :class="\Illuminate\Support\Arr::toCssClasses([
                                                    'absolute bottom-1 right-1 rtl:right-auto rtl:left-1' => $actionsPosition === ActionsPosition::BottomCorner,
                                                    'md:relative md:bottom-0 md:right-0 rtl:md:left-0' => $actionsPosition === ActionsPosition::BottomCorner && (! $contentGrid),
                                                    'mb-3' => $actionsPosition === ActionsPosition::AfterContent,
                                                    'md:mb-0' => $actionsPosition === ActionsPosition::AfterContent && (! $contentGrid),
                                                    'hidden' => $isReordering,
                                                ])"
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
                    </x-filament-support::grid>
                @else
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

                                @if ($isSelectionEnabled)
                                    <x-tables::checkbox.cell>
                                        <x-tables::checkbox
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
                                    :class="$getHiddenClasses($column)"
                                >
                                    {{ $column->getLabel() }}
                                </x-tables::header-cell>
                            @endforeach

                            @if (count($actions) && (! $isReordering) && $actionsPosition === ActionsPosition::AfterCells)
                                @if ($actionsColumnLabel)
                                    <x-tables::header-cell alignment="right">
                                        {{ $actionsColumnLabel }}
                                    </x-tables::header-cell>
                                @else
                                    <th class="w-5"></th>
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

                                    @if ($isSelectionEnabled)
                                        <td></td>
                                    @endif
                                @endif

                                @foreach ($columns as $column)
                                    <x-tables::cell class="px-4 py-1">
                                        @if ($column->isIndividuallySearchable())
                                            <x-tables::search-input wire-model="tableColumnSearchQueries.{{ $column->getName() }}" />
                                        @endif
                                    </x-tables::cell>
                                @endforeach

                                @if (count($actions) && (! $isReordering) && $actionsPosition === ActionsPosition::AfterCells)
                                    <td></td>
                                @endif
                            </x-tables::row>
                        @endif

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
                                :class="\Illuminate\Support\Arr::toCssClasses(array_merge(
                                    [
                                        'group cursor-move' => $isReordering,
                                    ],
                                    $getRecordClasses($record),
                                ))"
                            >
                                <x-tables::reorder.cell :class="\Illuminate\Support\Arr::toCssClasses([
                                    'hidden' => ! $isReordering,
                                ])">
                                    <x-tables::reorder.handle />
                                </x-tables::reorder.cell>

                                @if (count($actions) && $actionsPosition === ActionsPosition::BeforeCells)
                                    <x-tables::actions.cell
                                        :class="\Illuminate\Support\Arr::toCssClasses([
                                            'hidden' => $isReordering,
                                        ])"
                                    >
                                        <x-tables::actions
                                            :actions="$actions"
                                            :record="$record"
                                        />
                                    </x-tables::actions.cell>
                                @endif

                                @if ($isSelectionEnabled)
                                    <x-tables::checkbox.cell :class="\Illuminate\Support\Arr::toCssClasses([
                                        'hidden' => $isReordering,
                                    ])">
                                        <x-tables::checkbox
                                            x-model="selectedRecords"
                                            :value="$recordKey"
                                            class="filament-tables-record-checkbox"
                                        />
                                    </x-tables::checkbox.cell>
                                @endif

                                @if (count($actions) && $actionsPosition === ActionsPosition::BeforeColumns)
                                    <x-tables::actions.cell
                                        :class="\Illuminate\Support\Arr::toCssClasses([
                                            'hidden' => $isReordering,
                                        ])"
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
                                    @endphp

                                    <x-tables::cell
                                        :class="$getHiddenClasses($column)"
                                        wire:loading.remove.delay
                                        wire:target="{{ implode(',', \Filament\Tables\Table::LOADING_TARGETS) }}"
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

                                @if (count($actions) && $actionsPosition === ActionsPosition::AfterCells)
                                    <x-tables::actions.cell
                                        :class="\Illuminate\Support\Arr::toCssClasses([
                                            'hidden' => $isReordering,
                                        ])"
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
                    </x-tables::table>
                @endif

                @if (($content || $hasColumnsLayout) && $contentFooter)
                    {{ $contentFooter->with(['columns' => $columns, 'records' => $records]) }}
                @endif
            @else
                @if ($emptyState = $getEmptyState())
                    {{ $emptyState }}
                @else
                    <div class="flex items-center justify-center p-4">
                        <x-tables::empty-state :icon="$getEmptyStateIcon()" :actions="$getEmptyStateActions()">
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

        @if (
            $records instanceof \Illuminate\Contracts\Pagination\Paginator &&
            ((! $records instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) || $records->total())
        )
            <div @class([
                'filament-tables-pagination-container p-2 border-t',
                'dark:border-gray-700' => config('tables.dark_mode'),
            ])>
                <x-tables::pagination
                    :paginator="$records"
                    :records-per-page-select-options="$getRecordsPerPageSelectOptions()"
                />
            </div>
        @endif

        @if ($hasFiltersAfterContent)
            <div class="px-2 pb-2">
                <x-tables::hr />

                <div class="p-4 mt-2">
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
            :wire:key="$action ? $this->id . '.table' . ($getMountedActionRecordKey() ? '.records.' . $getMountedActionRecordKey() : null) . '.actions.' . $action->getName() . '.modal' : null"
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
                        <x-tables::modal.heading>
                            {{ $action->getModalHeading() }}
                        </x-tables::modal.heading>

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

                @php
                    $modalActions = array_filter(
                        $action->getModalActions(),
                        fn (\Filament\Support\Actions\Modal\Actions\Action $action): bool => ! $action->isHidden(),
                    );
                @endphp

                @if (count($modalActions))
                    <x-slot name="footer">
                        <x-tables::modal.actions :full-width="$action->isModalCentered()">
                            @foreach ($modalActions as $modalAction)
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
                        <x-tables::modal.heading>
                            {{ $action->getModalHeading() }}
                        </x-tables::modal.heading>

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

                @if (count($action->getModalActions()))
                    <x-slot name="footer">
                        <x-tables::modal.actions :full-width="$action->isModalCentered()">
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
